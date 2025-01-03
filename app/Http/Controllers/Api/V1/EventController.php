<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Interfaces\EventRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\GetEventsByCoordinatesRequest;
use App\Http\Requests\V1\GetEventsByInterestsRequest;
use App\Http\Requests\V1\GetEventsFeedRequest;
use App\Models\Event;
use App\Services\Events\EventService;
use App\Services\Events\EventViewService;
use App\Support\Point;
use App\Transformers\BaseTransformer;
use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    use Helpers;

    public function __construct(
        public EventViewService $eventViewService,
        public EventRepositoryInterface $repository,
        public EventService $service
    ) {
    }

    /**
     * Получение интересов текущего пользователя
     *
     * @return Response
     */
    public function index(): Response
    {
        $events = $this->repository->getAll();

        return $this->response->collection($events, BaseTransformer::class);
    }

    /**
     * Отображает страницу мероприятия и записывает просмотр.
     *
     * @param Event $event
     * @return Response
     */
    public function show(Event $event): Response
    {
        return $this->response->item($event, BaseTransformer::class);
    }

    /**
     * Вывод записей в соответствии с геолокацией
     *
     * @param GetEventsByCoordinatesRequest $request
     * @return Response
     */
    public function getByCoordinates(GetEventsByCoordinatesRequest $request): Response
    {
        $latitude = (float) $request->input('latitude');
        $longitude = (float) $request->input('longitude');

        // Получаем опциональные параметры из запроса
        $options = [
            'radius' => $request->input('radius'),
            'weight_popularity' => $request->input('weight_popularity'),
            'weight_distance' => $request->input('weight_distance'),
        ];

        // Получаем количество записей на страницу для пагинации
        $perPage = $request->input('per_page', 15); // По умолчанию 15 записей на страницу

        // Получаем номер страницы
        $page = $request->input('page', 1);

        // Получаем запрос событий
        $eventsQuery = $this->service->getEventsByLocation(new Point($latitude, $longitude), $options);

        // Пагинация
        $events = $eventsQuery->paginate($perPage, ['*'], 'page', $page);

        return $this->response->item($events, BaseTransformer::class);
    }

    /**
     * Получить мероприятия на основе интересов пользователя или переданных параметров.
     *
     * @param GetEventsByInterestsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByInterests(GetEventsByInterestsRequest $request)
    {
        // Попытка получить интересы из параметров запроса
        $interestIds = $request->input('interests'); // Ожидается массив ID интересов

        // Если пользователь аутентифицирован и интересы не указаны в параметрах
        if (Auth::check() && empty($interestIds)) {
            $user = Auth::user();
            $interestIds = $user->interests()->pluck('interests.id')->toArray();
        }

        // Если интересы всё ещё не заданы, можно вернуть сообщение или использовать дефолтные значения
        if (empty($interestIds)) {
            return response()->json([
                'message' => 'Интересы не указаны и не найдены у пользователя.'
            ], 400);
        }

        // Получаем опциональные параметры из запроса
        $options = [
            'weight_popularity' => $request->input('weight_popularity'),
            'weight_interest_match' => $request->input('weight_interest_match'),
        ];

        // Получаем количество записей на страницу для пагинации
        $perPage = $request->input('per_page', 15); // По умолчанию 15 записей на страницу

        // Получаем номер страницы
        $page = $request->input('page', 1);

        // Получаем запрос мероприятий из сервиса
        $eventsQuery = $this->service->getEventsByInterests($interestIds, $options);

        // Пагинация
        $events = $eventsQuery->paginate($perPage, ['*'], 'page', $page);

        // Возвращаем данные (используйте трансформер или ресурс, если требуется)
        return response()->json($events);
    }

    /**
     * Возвращает ленту мероприятий с использованием курсорной пагинации.
     *
     * @param GetEventsFeedRequest $request
     * @return mixed
     */
    public function feed(GetEventsFeedRequest $request): mixed
    {
        // TODO: Параметры фильтрации
        // Получаем координаты
        $coordinates = new Point(
            $request->input('latitude'),
            $request->input('longitude')
        );

        // Получаем дополнительные параметры
        $parameters = [
            'cursor' => $request->input('cursor'),
            'per_page' => $request->input('per_page', 20),
            'is_actual' => $request->input('is_actual', true),
        ];

        $interestIds = $request->input('interest_ids');

        try {
            // Получаем ленту мероприятий
            $events = $this->service->getEventsFeed($coordinates, $interestIds ?? [], $parameters);

            // TODO: Отображать сообщение и версию api
            return $events;
        } catch (\Exception $e) {
            // Логируем ошибку
            Log::error('Ошибка при получении ленты мероприятий: ' . $e->getMessage());

            $this->response->error('Произошла ошибка при получении ленты мероприятий', 500);
        }
    }

    /**
     * Подсказки
     *
     * @param Request $request
     * @return Response
     */
    public function getSuggestions(Request $request)
    {
        $query = $request->input('query');

        if (empty($query)) {
            return $this->response->collection(collect(), BaseTransformer::class);
        }

        $suggestions = $this->service->getEventSuggestions($query);

        return $this->response->collection($suggestions, BaseTransformer::class);
    }

    /**
     * Поиск мероприятий
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchEvents(Request $request)
    {
        // TODO: Вынести в Request
        $query = $request->input('query');

        if (empty($query)) {
            return response()->json([]);
        }

        $filters = [
            'interests' => $request->input('interests', []),
            'location' => $request->input('location', null), // ['latitude' => ..., 'longitude' => ...]
            'radius' => $request->input('radius', null),
            'timeRange' => $request->input('timeRange', null), // ['start' => ..., 'end' => ...]
        ];

        $eventsQuery = $this->service->searchEvents($query, $filters);

        // TODO: Вынести в обертку (метод feed)
        $eventsQuery->with('attachments');
        $eventsQuery->with('eventGroup');

        $events = $eventsQuery->paginate(20);

        return response()->json($events);
    }
}
