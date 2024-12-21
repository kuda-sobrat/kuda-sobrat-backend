<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Interfaces\EventRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\GetEventsByCoordinatesRequest;
use App\Models\Event;
use App\Services\Events\EventService;
use App\Services\Events\EventViewService;
use App\Support\Point;
use App\Transformers\BaseTransformer;
use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
}
