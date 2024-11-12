<?php

namespace App\Listeners;

use App\Events\CommunityVerifiedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CommunityVerifiedListener
{
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
    public function handle(CommunityVerifiedEvent $event): void
    {
        dd($event);
    }
}
