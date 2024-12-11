<?php

namespace App\Repositories;

use App\Contracts\Interfaces\EventAttendeeRepositoryInterface;
use App\Models\Event;
use App\Models\User;
use Exception;

class EventParticipationService
{
    public function __construct(
        protected EventAttendeeRepositoryInterface $repository,
    ) {
    }

    /**
     * Присоединяет пользователя к мероприятию.
     *
     * @param Event $event
     * @param User $user
     * @throws Exception
     */
    public function join(Event $event, User $user): void
    {
        // Проверяем, не является ли пользователь уже участником
        if ($this->repository->exists($event->id, $user->id)) {
            throw new Exception('Вы уже являетесь участником данного мероприятия.');
        }

        // TODO: Дополнительные проверки (например, проверка статуса мероприятия)
//        if (!$event->isActive()) {
//            throw new Exception('Регистрация на это мероприятие закрыта.');
//        }

        // Проверка на максимальное количество участников
//        if ($event->attendees()->count() >= $event->max_participants) {
//            throw new Exception('Достигнуто максимальное количество участников.');
//        }

        // Добавляем участника
        $this->repository->create([
            'event_id' => $event->id,
            'user_id'  => $user->id,
        ]);

        // Обновление счетчиков в событии
        $event->increment('attendees');
    }

    /**
     * Удаляет пользователя из участников мероприятия.
     *
     * @param Event $event
     * @param User $user
     * @throws Exception
     */
    public function leave(Event $event, User $user): void
    {
        if (!$this->repository->exists($event->id, $user->id)) {
            throw new Exception('Вы не являетесь участником данного мероприятия.');
        }

        $this->repository->deleteByEventAndUser($event->id, $user->id);

        // Обновление счетчиков в событии
        $event->decrement('attendees');
    }
}
