<?php

namespace App\Jobs;

use App\Enums\ProcessStatusEnum;
use App\Events\CommunitiesVerifiedEvent;
use App\Models\Community;
use App\Models\ContextPost;
use App\Repositories\CommunityRepository;
use App\Services\CommunityVerificationService;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

class VerifyPostCollectedCommunityJob implements ShouldQueue
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
     * @throws Throwable
     */
    public function handle(
        CommunityRepository $communityRepository,
        CommunityVerificationService $communityVerificationService,
    ): void
    {
        $community = $communityRepository->get($this->communityId);

        if (!$community) {
            // TODO: Логирование
            return;
        }

        // Создаем задания для каждого поста
        $jobs = [];
        foreach ($community->contextPosts as $contextPost) {
            if ($contextPost->status === ProcessStatusEnum::Created) {
                $jobs[] = new ProcessContextJob($contextPost->id, ContextPost::class);
            }
        }

        // 1. Проверить что хотябы одна из записей сообщества имеет мероприятие
        // 2. Составить интересы сообщества
        // 3. Получить геолокацию

        if (!empty($jobs)) {
            Bus::batch($jobs)
                ->then(static function (Batch $batch) use ($communityVerificationService, $community) {
                    $communityVerificationService->verifyCommunity($community);
                    event(new CommunitiesVerifiedEvent());
                })
                ->dispatch();
        } else {
            $communityVerificationService->verifyCommunity($community);
        }
    }
}
