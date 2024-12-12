<?php

namespace App\Console\Commands;

use App\Contracts\Interfaces\GeocodingServiceInterface;
use App\Jobs\VerifyCommunityJob;
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
        VerifyCommunityJob::dispatch(2);
//        $contextEvent = ContextEvent::first();
//        dd($contextEvent->contextPost);
//        $contextService->processContext(349, ContextResponse::class);
//        $contextService->processContext(59, ContextEvent::class);
//        $contextService->processContext(1, ContextPost::class);
//        $geocoder = new NominatimProvider();
//        $result = $geocoder->getAllCoordinatesForAddress('ул. Плехановская, 22, Воронеж');
//        dd($result);
    }
}
