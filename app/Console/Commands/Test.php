<?php

namespace App\Console\Commands;

use App\Models\Interest;
use App\Services\Events\EventService;
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
        EventService $service
    )
    {
//        /** @var Interest $interest */
//        $interest = Interest::query()->first();
//        $interests = $interest->getUnfoldedInterests();

        $events = $service->getEventsByInterests([181]);
        dd($events->get()->pluck(['is_interest_matched']));
    }
}
