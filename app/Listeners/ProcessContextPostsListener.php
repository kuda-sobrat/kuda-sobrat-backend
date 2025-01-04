<?php

namespace App\Listeners;

use App\Console\Commands\ProcessContextPostsCommand;
use App\Events\CommunitiesVerifiedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessContextPostsListener implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommunitiesVerifiedEvent $event): void
    {
        (new ProcessContextPostsCommand())->handle();
    }
}
