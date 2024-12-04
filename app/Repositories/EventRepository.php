<?php

namespace App\Repositories;

use App\Contracts\Interfaces\EventRepositoryInterface;
use App\Enums\ProcessStatusEnum;
use App\Models\Event;
use Illuminate\Support\Collection;

class EventRepository implements EventRepositoryInterface
{

    /**
     * Получение всех мероприятий
     *
     * @return Collection<Event>
     */
    public function getAll(): Collection
    {
        return Event::query()
            ->where('status', '=', ProcessStatusEnum::Completed->value)
            ->with('contextPosts')
            ->with('attachments', function ($query) {
                return $query->where('type', '=', 'photo');
            })
            ->get();
    }
}
