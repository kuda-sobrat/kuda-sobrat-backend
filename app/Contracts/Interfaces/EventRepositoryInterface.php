<?php

namespace App\Contracts\Interfaces;

use App\Models\Event;
use Illuminate\Support\Collection;

interface EventRepositoryInterface
{
    /**
     * Получение мероприятия по id
     *
     * @param $id
     * @return Event|null
     */
    public function find($id): ?Event;

    /**
     * Получение всех интересов
     *
     * @return Collection<Event>
     */
    public function getAll(): Collection;

    /**
     * Увеличить счетчик просмотров
     *
     * @param Event $event
     * @return void
     */
    public function incrementViews(Event $event): void;
}
