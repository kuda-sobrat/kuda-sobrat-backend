<?php

namespace App\Contracts\Interfaces;

use App\Models\EventAttendee;

interface EventAttendeeRepositoryInterface
{
    /**
     * Создание записи
     *
     * @param array $data
     * @return EventAttendee
     */
    public function create(array $data): EventAttendee;

    /**
     * Удаление по мероприятию и пользователю
     *
     * @param int $eventId
     * @param int $userId
     * @return bool
     */
    public function deleteByEventAndUser(int $eventId, int $userId): bool;

    /**
     * Проверка на существование
     *
     * @param int $eventId
     * @param int $userId
     * @return bool
     */
    public function exists(int $eventId, int $userId): bool;
}
