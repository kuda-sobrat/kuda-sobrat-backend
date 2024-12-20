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
    protected $signature = 'communities:collect {community_id?}';

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
        $communityId = $this->argument('community_id');

        if ($communityId) {
            // Обработка только указанного сообщества
            $community = Community::find($communityId);

            if (!$community) {
                $this->error("Сообщество с ID {$communityId} не найдено.");
                return;
            }

            $job = (new CollectCommunityPostsJob($community->id))
                ->chain([
                    new ProcessCollectedPostsJob($community->id)
                ]);

            Bus::batch([$job])
                ->name("Обработка сообщества ID {$community->id}")
                ->dispatch();

            $this->info("Задача по обработке сообщества ID {$community->id} добавлена в очередь.");
        } else {
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
}
