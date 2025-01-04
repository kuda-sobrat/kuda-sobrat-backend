<?php

namespace App\Jobs;

use App\Repositories\CommunityRepository;
use App\Services\CommunityVerificationService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerifyCommunityPostsJob implements ShouldQueue
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
        CommunityVerificationService $communityVerificationService,
    ): void
    {
        $community = $communityRepository->get($this->communityId);

        if (!$community) {
            dump('Community not found');
            // TODO: Логирование
            return;
        }
        $communityVerificationService->verifyCommunityPosts($community);
    }
}
