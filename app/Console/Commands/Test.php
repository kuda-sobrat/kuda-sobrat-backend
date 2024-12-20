<?php

namespace App\Console\Commands;

use App\Models\ContextEvent;
use App\Models\Event;
use App\Repositories\EventRepository;
use App\Repositories\InterestRepository;
use App\Services\CommunityService;
use App\Services\CommunityVerificationService;
use App\Services\Context\ContextService;
use App\Services\Events\EventService;
use App\Support\Point;
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
//        dd(571);
//        CommunityLocationDetectionJob::dispatch(6);
//        $contextEvent = ContextEvent::first();
//        dd($contextEvent->contextPost);
//        $contextService->processContext(349, ContextResponse::class);
        $contextService->processContext(559, ContextEvent::class);
        $contextService->processContext(571, ContextEvent::class);
//        $contextService->processContext(3022, ContextPost::class);
//        $geocoder = new NominatimProvider();
//        $result = $geocoder->getAllCoordinatesForAddress('ул. Плехановская, 22, Воронеж');
//        dd($result);
    }
}
