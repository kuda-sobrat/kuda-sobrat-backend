<?php

namespace App\Console\Commands;

use App\Jobs\FetchCommunityPostFromApiJob;
use App\Repositories\CommunityRepository;
use Illuminate\Console\Command;

/**
 *
 */
class GetCommunityPost extends Command
{
    protected $signature = 'community:post {id}';
    protected $description = 'Получает последний пост сообщества';

    public function handle(
        CommunityRepository $communityRepository,
    )
    {
        $community = $communityRepository->get((int)$this->argument('id'));

        if (!$community) {
            // TODO: Логирование
            dump("Сообщество с ID {$this->argument('id')} не найдено.");
            return;
        }

        FetchCommunityPostFromApiJob::dispatch($community->id);
        // Обновляем время последней проверки
        $community->last_checked_at = now();
        $community->save();
    }
}
