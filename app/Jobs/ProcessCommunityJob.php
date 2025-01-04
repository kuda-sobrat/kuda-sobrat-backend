<?php

namespace App\Jobs;

use App\Models\Community;
use App\Services\CommunityService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCommunityJob implements ShouldQueue
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
     * Execute the job.
     */
    public function handle(CommunityService $communityService): void
    {
        /** @var Community $community */
        $community = Community::query()->find($this->communityId);

        if (!$community) {
            // TODO: Логирование
            dump("Community with ID {$this->communityId} not found.");
            return;
        }

        // TODO: Перерассмотреть условие (интервал времени)
        if (empty($community->updated_at) || $community->updated_at->diffInHours(now()) >= 24) {
            $communityService->updateCommunityInfo($community);
        }

        if (!$community->is_verified) {
            $communityService->verifyCommunity($community);
        }
    }
}
