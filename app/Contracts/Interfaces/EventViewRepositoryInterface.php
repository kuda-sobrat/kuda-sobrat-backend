<?php

namespace App\Contracts\Interfaces;

use App\Models\EventView;

interface EventViewRepositoryInterface
{
    /**
     * Найти просмотр через мероприятие и UserAgent
     *
     * @param $eventId
     * @param $userAgent
     * @param $userId
     * @param $sessionId
     * @return EventView|null
     */
    public function findByEventAndUserAgent($eventId, $userAgent, $userId = null): ?EventView;

    /**
     * Обновить просмотр
     *
     * @param EventView $eventView
     * @param array $data
     * @return void
     */
    public function update(EventView $eventView, array $data): void;

    /**
     * Создание просмотра
     *
     * @param array $data
     * @return EventView
     */
    public function create(array $data): EventView;
}
