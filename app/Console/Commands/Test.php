<?php

namespace App\Console\Commands;

use App\Repositories\EventRepository;
use App\Repositories\InterestRepository;
use App\Services\CommunityService;
use App\Services\CommunityVerificationService;
use App\Services\Context\ContextService;
use App\Services\Events\EventService;
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
