<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Interfaces\EventRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventViewService;
use App\Transformers\BaseTransformer;
use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;

class EventController extends Controller
{
    use Helpers;

    public function __construct(
        public EventViewService $eventViewService,
        public EventRepositoryInterface $repository,
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
}
