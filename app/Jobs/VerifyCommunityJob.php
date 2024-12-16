<?php

namespace App\Jobs;

use App\Repositories\CommunityRepository;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerifyCommunityJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public $communityId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(
        CommunityRepository $communityRepository,
    ): void {
        $community = $communityRepository->get($this->communityId);

        if (!$community) {
            // TODO: Логирование
            dump("Сообщество с ID {$this->communityId} не найдено.");
//            Log::warning("Сообщество с ID {$this->communityId} не найдено.");
            return;
        }

        try {
            $latestSavedPost = $communityRepository->getLatestPost($community);
            // Устанавливаем статус верификации в 'pending'
            $community->verification_status = 'pending';
            $community->save();

            if (empty($latestSavedPost) || (!empty($latestSavedPost) && $latestSavedPost->created_at < Carbon::now()->subDays(7))) {
                CollectCommunityPostsJob::withChain([
                    new ProcessCollectedPostsJob($community->id),
                    new CommunityLocationDetectionJob($community->id),
                    new VerifyCommunityPostsJob($community->id)
                ])->dispatch($community->id);
            } else {
                VerifyCommunityPostsJob::dispatch($community->id);
            }
        } catch (\Exception $e) {
            dump($e->getMessage());
            // TODO: Логирование
            // В случае ошибки устанавливаем статус 'failed'
            $community->verification_status = 'failed';
            $community->save();
        }
    }
}
