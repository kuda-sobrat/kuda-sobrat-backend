<?php

namespace App\Jobs;

use App\Repositories\CommunityRepository;
use App\Repositories\ContextPostsRepository;
use App\Services\CommunityService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Получает список последних постов для сообщества, ориентируясь по последнему записанному посту
 * Если посты не определены забирает последние 8 постов
 */
class FetchCommunityPostFromApiJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected $communityId,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(
        CommunityRepository $communityRepository,
        CommunityService $communityService,
        ContextPostsRepository $contextPostsRepository,
    ): void
    {
        try {
            $community = $communityRepository->get($this->communityId);
            $latestSavedPost = $communityRepository->getLatestPost($community);

            // TODO: Получить последние посты сообщества по дате, если верификация пройдена
            $contextPosts = $communityService->getLatestPosts($community, limit: 1);
            $contextPosts[0]->save();
            $contextPostsRepository->savePosts($contextPosts);
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }
}
