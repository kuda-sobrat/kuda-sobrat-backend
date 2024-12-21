<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateEventPopularity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:update-popularity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет значение популярности для актуальных событий';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаем обновление популярности событий...');

        // where('updated_at', '<=', now()->subHour())->
        Event::where('is_archived', '=', false)->chunkById(1000, function ($events) {
            $updateData = [];
            foreach ($events as $event) {
                $popularity = DB::selectOne(
                    'SELECT calculate_popularity(?, ?, ?, ?) AS popularity',
                    [
                        $event->id,
                        $event->views,
                        $event->shares,
                        $event->created_at,
                    ]
                )->popularity;

                $updateData[] = [
                    'id' => $event->id,
                    'popularity_score' => $popularity,
                ];
            }

            // Массовое обновление
            foreach ($updateData as $data) {
                Event::where('id', $data['id'])->update(['popularity_score' => $data['popularity_score']]);
            }
        });

        $this->info('Обновление популярности завершено.');
    }
}
