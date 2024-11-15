<?php

namespace App\Contracts\Interfaces;

use App\Models\Event;
use Illuminate\Support\Collection;

interface EventRepositoryInterface
{
    /**
     * Получение всех интересов
     *
     * @return Collection<Event>
     */
    public function getAll(): Collection;
}
