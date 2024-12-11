<?php

namespace App\Repositories;

use App\Contracts\Interfaces\EventShareRepositoryInterface;
use App\Models\EventShare;

class EventShareRepository implements EventShareRepositoryInterface
{
    /**
     * Создание новой записи
     *
     * @param array $data
     * @return EventShare
     */
    public function create(array $data): EventShare
    {
        return EventShare::create($data);
    }

    public function firstOrCreate(array $attributes, array $values = []): EventShare
    {
        return EventShare::firstOrCreate($attributes, $values);
    }
}
