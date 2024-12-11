<?php

namespace App\Console\Commands;

use App\Jobs\CollectCommunityPostsJob;
use App\Jobs\ProcessCollectedPostsJob;
use App\Models\Community;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class CollectCommunitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'communities:collect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Собирает актуальную информацию о всех сохраненных сообществах. Верифицирует при надобности';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batchSize = 100; // Размер пакета (можете изменить по необходимости)

        Community::chunk($batchSize, function ($communities) {
            $jobs = [];

            foreach ($communities as $community) {
                $jobs[] = (new CollectCommunityPostsJob($community->id))
                    ->chain([
                        new ProcessCollectedPostsJob($community->id)
                    ]);
            }

            // Создаем пакет задач
            Bus::batch($jobs)
                ->name('Обработка пачки сообществ')
                ->dispatch();
        });

        $this->info('Задачи по обработке сообществ добавлены в очередь.');
    }
}
