<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

class DeleteEvents extends Command
{
    protected $signature = 'events:delete';
    protected $description = 'Удаляет события, помеченные для удаления, по истечении заданного периода.';

    public function handle()
    {
        $this->info('Начало удаления событий...');

        $deletionDelayDays = config('event_cleanup.deletion_delay_days', 7); // Количество дней после пометки на удаление для фактического удаления

        Event::whereNotNull('marked_for_deletion_at')
            ->where('marked_for_deletion_at', '<', now()->subDays($deletionDelayDays))
            ->chunkById(100, function ($events) {
                foreach ($events as $event) {
                    $eventId = $event->id;

                    // Используем мягкое удаление (soft delete)
                    $event->delete();

                    $this->info("Событие ID {$eventId} удалено.");
                }
            });

        $this->info('Удаление событий завершено.');
    }
}
