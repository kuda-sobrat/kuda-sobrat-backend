<?php

namespace App\Console\Commands;

use App\Contracts\Interfaces\GeocodingServiceInterface;
use App\Providers\NominatimProvider;
use App\Repositories\EventRepository;
use App\Repositories\InterestRepository;
use App\Services\CommunityService;
use App\Services\CommunityVerificationService;
use App\Services\Context\ContextService;
use App\Services\Events\EventService;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Spatie\Geocoder\Geocoder;

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
        $geocoder = new NominatimProvider();
        dd($geocoder->getAllCoordinatesForAddress('Воронеж, ул. Театральная, 17'));
//        $communityService->getCommunityInfo(3);
//        dd($interestRepository->getInterestsByLevel(2)->pluck(['name']));
    }
}
