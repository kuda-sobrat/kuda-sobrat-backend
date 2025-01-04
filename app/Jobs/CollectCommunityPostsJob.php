<?php

namespace App\Jobs;

use App\Repositories\CommunityRepository;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CollectCommunityPostsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected $communityId
    ) {
    }

    /**
     * TODO: Пересмотреть
     * Execute the job.
     */
    public function handle(
        CommunityRepository $communityRepository
    ): void
    {
        $community = $communityRepository->get($this->communityId);

        if (!$community) {
            // TODO: Логирование
            dump("Сообщество с ID {$this->communityId} не найдено.");
            return;
        }

        FetchCommunityPostsFromApiJob::dispatch($community->id);
        // Обновляем время последней проверки
        $community->last_checked_at = now();
        $community->save();
    }
}
