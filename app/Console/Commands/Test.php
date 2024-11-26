<?php

namespace App\Console\Commands;

use App\Events\CommunityVerifiedEvent;
use App\Jobs\FetchEventInterestsFromGPTJob;
use App\Jobs\TestJob;
use App\Models\Community;
use App\Models\ContextPost;
use App\Models\ContextResponse;
use App\Repositories\EventRepository;
use App\Repositories\InterestRepository;
use App\Services\CommunityService;
use App\Services\CommunityVerificationService;
use App\Services\Context\ContextService;
use App\Services\EventService;
use App\Services\InterestService;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(
        CommunityVerificationService $communityVerificationService,
        ContextService $contextService,
        InterestRepository $interestRepository,
        EventService $eventService,
        EventRepository $eventRepository,
        CommunityService $communityService,
    )
    {
        $events = $eventRepository->getAll();
        foreach ($events as $event) {
            foreach ($event->contextPosts as $contextPost) {
                dump($contextPost->id);
            }
        }
//        $communityService->getCommunityInfo(3);
//        dd($interestRepository->getInterestsByLevel(2)->pluck(['name']));
    }
}
