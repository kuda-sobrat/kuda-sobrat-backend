<?php

namespace App\Repositories;

use App\Contracts\Interfaces\EventViewRepositoryInterface;
use App\Models\EventView;

class EventViewRepository implements EventViewRepositoryInterface
{
    public function findByEventAndUserAgent($eventId, $userAgent, $userId = null, $sessionId = null): ?EventView
    {
        $query = EventView::where('event_id', '=', $eventId)
            ->where('user_agent', '=', $userAgent);

        if ($userId) {
            $query->where('user_id', '=', $userId);
        }

        return $query->first();
    }

    public function update(EventView $eventView, array $data): void
    {
        $eventView->update($data);
    }

    public function create(array $data): EventView
    {
        // TODO: Подчеркивание
        return EventView::query()->create($data);
    }
}
