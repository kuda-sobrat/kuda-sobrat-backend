<?php

namespace App\Services\Events;

use App\Contracts\Interfaces\EventRepositoryInterface;
use App\Contracts\Interfaces\EventViewRepositoryInterface;
use App\Models\Event;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

/**
 * Сервис просмотров мероприятий (событий)
 */
class EventViewService
{
    public function __construct(
        protected Request $request,
        protected Guard $auth,
        protected EventRepositoryInterface $eventRepository,
        protected EventViewRepositoryInterface $eventViewRepository,
    ) {
    }

    /**
     * Сохраняет просмотр мероприятия с заменой существующей записи по уникальным полям.
     *
     * @param Event $event
     * @return void
     */
    public function recordView(Event $event): void
    {
        /** @var User|null $user */
        $user = $this->auth->user();
        $userId = $user?->id;
        $ipAddress = $this->request->ip();
        $userAgent = $this->request->header('User-Agent');

        // Попытка найти существующую запись
        $eventView = $this->eventViewRepository->findByEventAndUserAgent(
            $event->id,
            $userAgent,
            $userId,
        );

        if ($eventView) {
            // Обновляем поля существующей записи
            $this->eventViewRepository->update($eventView, [
                'ip_address' => $ipAddress,
                'viewed_at'  => now(),
            ]);
        } else {
            // Создаем новую запись
            $this->eventViewRepository->create([
                'event_id'   => $event->id,
                'user_id'    => $userId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
            // Увеличиваем счетчик просмотров
            $this->eventRepository->incrementViews($event);
        }
    }
}
