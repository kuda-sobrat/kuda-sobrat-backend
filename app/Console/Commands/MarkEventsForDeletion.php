<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

class MarkEventsForDeletion extends Command
{
    protected $signature = 'events:mark-for-deletion';
    protected $description = 'Помечает архивные события для удаления по заданным критериям.';

    public function handle()
    {
        $this->info('Начало пометки событий для удаления...');

        $deletionMarkDelayDays = config('event_cleanup.deletion_mark_delay_days', 30); // Количество дней после архивации для пометки на удаление
        $attendeeThreshold = config('event_cleanup.attendee_threshold', 50); // Пороговое количество участников
        $popularityThreshold = config('event_cleanup.popularity_threshold', 10); // Пороговый показатель популярности

        Event::where('is_archived', true)
            ->whereNull('marked_for_deletion_at')
            ->where('archived_at', '<', now()->subDays($deletionMarkDelayDays))
            ->chunkById(100, function ($events) use ($attendeeThreshold, $popularityThreshold) {
                foreach ($events as $event) {
                    $attendeesCount = $event->attendees()->count();

                    if ($attendeesCount < $attendeeThreshold && $event->popularity_score < $popularityThreshold) {
                        $event->update(['marked_for_deletion_at' => now()]);

                        $this->info("Событие ID {$event->id} помечено для удаления.");
                    }
                }
            });

        $this->info('Пометка событий для удаления завершена.');
    }
}
