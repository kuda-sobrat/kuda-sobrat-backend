<?php

namespace App\Repositories;

use App\Contracts\Interfaces\EventRepositoryInterface;
use App\Enums\ProcessStatusEnum;
use App\Models\Event;
use Illuminate\Support\Collection;

class EventRepository implements EventRepositoryInterface
{
    /**
     * Получить мероприятие по id
     *
     * @param $id
     * @return Event|null
     */
    public function find($id): ?Event
    {
        // TODO: Подчеркивание
        return Event::query()->find($id);
    }

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

    /**
     * Увеличить кол-во просмотров
     *
     * @param Event $event
     * @return void
     */
    public function incrementViews(Event $event): void
    {
        $event->increment('views');
    }
}
