<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventInterest;
use App\Models\Interest;

class EventInterestMatchingService
{
    public function matchInterests(Event $event, array $tags)
    {
        foreach ($tags as $tag) {
            $interest = Interest::firstOrCreate(['name' => $tag]);

            EventInterest::firstOrCreate([
                'event_id' => $event->id,
                'interest_id' => $interest->id,
            ]);
        }
    }
}
