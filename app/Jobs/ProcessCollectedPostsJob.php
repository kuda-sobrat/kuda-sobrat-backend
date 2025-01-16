<?php

namespace App\Jobs;

use App\Enums\ProcessStatusEnum;
use App\Models\ContextPost;
use App\Repositories\CommunityRepository;
use App\Services\CommunityVerificationService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProcessCollectedPostsJob implements ShouldQueue
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
    ): void
    {
        dump('Процедурная очередь постов');
        $community = $communityRepository->get($this->communityId);

        if (!$community) {
            dump('Сообщество не определенно ' . $this->communityId);
            // TODO: Логирование
            return;
        }

        foreach ($community->contextPosts as $contextPost) {
            if ($contextPost->status === ProcessStatusEnum::Created || $contextPost->status === ProcessStatusEnum::Failed) {
                // TODO: обработка ошибок
                try {
                    ProcessContextJob::dispatch($contextPost->id, ContextPost::class);
                } catch (\Exception $e) {
                    dump($e->getMessage());
                }
            }
        }
    }
}
