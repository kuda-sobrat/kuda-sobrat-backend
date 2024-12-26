<?php

namespace App\Console\Commands;

use App\Models\Interest;
use App\Services\Events\EventService;
use App\Services\InterestService;
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
        EventService $service
    )
    {
        $events = $service->getEventsFeed(new Point(39.1967, 51.666), [181], ['per_page' => 20]);
        dd($events);
//        dd(collect($events->items()));
    }
}
