<?php

namespace App\Console\Commands;

use App\Models\ContextEvent;
use App\Models\Event;
use App\Models\EventSource;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * Определяет источники и связанные сообщества по context_events.
 */
class LinkingEventsCommand extends Command
{
    protected $signature = 'app:linking-events';

    protected $description = 'Определяет источники и связанные сообщества по context_events';

    public function handle()
    {
        /** @var Collection<Event> $items */
        $events = Event::query()->get();

        $events->map(function (Event $event) {
            /** @var ?ContextEvent $contextEvent */
            $contextEvent = ContextEvent::query()->where('event_group_id', '=', $event->eventGroup->id)->first();
            $event->communities()->syncWithoutDetaching($contextEvent->community_id);

            EventSource::query()->updateOrCreate([
                'event_id' => $event->id,
                'social_link_id' => $contextEvent->contextPost->social_link_id,
                'source_id' => $contextEvent->contextPost->source_id,
            ]);
        });
    }
}
