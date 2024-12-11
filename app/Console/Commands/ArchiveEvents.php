<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

class ArchiveEvents extends Command
{
    protected $signature = 'events:archive';
    protected $description = 'Архивирует события, которые завершились.';

    public function handle()
    {
        $this->info('Начало архивации завершенных событий...');

        // Задаем срок окончания для событий без end_datetime
        $defaultEventDurationHours = 24;

        Event::where('is_archived', false)
            ->where(function ($query) use ($defaultEventDurationHours) {
                $query->where(function ($query) {
                    // События с end_datetime, которые завершились
                    $query->whereNotNull('end_datetime')
                        ->where('end_datetime', '<', now());
                })
                    ->orWhere(function ($query) use ($defaultEventDurationHours) {
                        // События без end_datetime, считаем завершенными через defaultEventDurationHours после start_datetime
                        $query->whereNull('end_datetime')
                            ->where('start_datetime', '<', now()->subHours($defaultEventDurationHours));
                    });
            })
            ->chunkById(100, function ($events) {
                foreach ($events as $event) {
                    $event->update([
                        'is_archived' => true,
                        'archived_at' => now(),
                    ]);

                    $this->info("Событие ID {$event->id} заархивировано.");
                }
            });

        $this->info('Архивация событий завершена.');
    }
}
