<?php

namespace App\Repositories;

use App\Contracts\Interfaces\EventRepositoryInterface;
use App\Models\Event;
use Illuminate\Support\Collection;

class EventRepository implements EventRepositoryInterface
{

    /**
     * Получение всех интересов
     *
     * @return Collection<Event>
     */
    public function getAll(): Collection
    {
        return Event::all();
    }
}
