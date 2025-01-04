<?php

namespace App\Repositories;

use App\Contracts\Interfaces\EventAttendeeRepositoryInterface;
use App\Models\EventAttendee;

/**
 * Репозиторий статуса участия пользователя
 */
class EventAttendeeRepository implements EventAttendeeRepositoryInterface
{
    public function create(array $data): EventAttendee
    {
        return EventAttendee::create($data);
    }

    public function deleteByEventAndUser(int $eventId, int $userId): bool
    {
        return EventAttendee::where('event_id', $eventId)
                ->where('user_id', $userId)
                ->delete() > 0;
    }

    public function exists(int $eventId, int $userId): bool
    {
        return EventAttendee::where('event_id', $eventId)
            ->where('user_id', $userId)
            ->exists();
    }
}
