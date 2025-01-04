<?php

namespace App\Console\Commands;

use App\Jobs\CollectCommunityPostsJob;
use App\Repositories\CommunityRepository;
use App\Services\CommunityService;
use Illuminate\Console\Command;

class CollectCommunityPostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'communities:collect-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получает последние посты верифицированных сообществ';

    /**
     * Execute the console command.
     */
    public function handle(
        CommunityRepository $communityRepository,
    )
    {
        $communityRepository->getCommunitiesToCheck()->chunk(100, function ($communities) {
            foreach ($communities as $community) {
                CollectCommunityPostsJob::dispatch($community->id);
                $this->info("Сообщество {$community->id} поставлено в очередь на обновление.");
            }
        });

        $this->info('Получение постов завершено.');
    }
}
