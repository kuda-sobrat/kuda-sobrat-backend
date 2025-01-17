<?php

namespace App\Console\Commands;

use App\Enums\ProcessStatusEnum;
use App\Models\Community;
use App\Models\ContextEvent;
use App\Models\ContextPost;
use App\Models\Event;
use App\Models\EventSource;
use App\Models\Interest;
use App\Services\Context\ContextService;
use App\Services\Events\EventService;
use App\Services\InterestService;
use App\Support\Point;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

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
    public function handle()
    {

//        dd($events[0]->toArray());
    }
}
