<?php

namespace App\Services\Events;

use App\Contracts\Interfaces\EventShareRepositoryInterface;
use App\Models\Event;
use App\Models\SocialNetwork;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Сервис шаринга мероприятий
 */
class EventShareService
{
    public function __construct(
        protected Request $request,
        protected Guard $auth,
        protected EventShareRepositoryInterface $repository,
    ) {
    }

    /**
     * Обрабатывает действие "поделиться" мероприятием.
     *
     * @param Event $event
     * @param SocialNetwork $socialNetwork
     * @return void
     */
    public function shareEvent(Event $event, SocialNetwork $socialNetwork): void
    {
        if (!Auth::user()->id) {
            throw new \InvalidArgumentException('Пользователь не определен');
        }

        // Определяем атрибуты для поиска или создания
        $attributes = [
            'event_id'          => $event->id,
            'user_id'           => Auth::user()->id,
            'social_network_id' => $socialNetwork->id,
        ];

        // Значения для нового создания
        $values = [
            'shared_at' => now(),
        ];

        // Пытаемся найти или создать новую запись
        $eventShare = $this->repository->firstOrCreate($attributes, $values);

        if ($eventShare->wasRecentlyCreated) {
            // Инкрементируем счетчик в событии
            $event->increment('shares');
        }
    }
}
