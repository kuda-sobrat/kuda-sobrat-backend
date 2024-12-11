<?php

namespace App\Contracts\Interfaces;

use App\Models\EventShare;

interface EventShareRepositoryInterface
{
    /**
     * Создание новой записи
     *
     * @param array $data
     * @return EventShare
     */
    public function create(array $data): EventShare;

    /**
     * Получить первую запись или создать новую
     *
     * @param array $attributes
     * @param array $values
     * @return EventShare
     */
    public function firstOrCreate(array $attributes, array $values = []): EventShare;
}
