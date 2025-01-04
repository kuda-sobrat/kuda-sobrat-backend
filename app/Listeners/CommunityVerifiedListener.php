<?php

namespace App\Listeners;

use App\Events\CommunityVerifiedEvent;
use App\Jobs\FetchCommunityInterestsFromGPTJob;
use App\Repositories\CommunityRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Обработка события завершения верификации сообщества.
 */
class CommunityVerifiedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected CommunityRepository $communityRepository,
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommunityVerifiedEvent $event): void
    {
        FetchCommunityInterestsFromGPTJob::dispatch($event->communityId);
    }
}
