<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateEventInterestsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected $eventId,
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // TODO:
//        $event = Event::with('community.interests')->find($this->eventId);
//
//        if (!$event) {
//            \Log::error("Event not found with ID: " . $this->eventId);
//            return;
//        }
//
//        $communityInterests = $event->community->interests;
//
//        foreach ($communityInterests as $interest) {
//            // Применяем логику формирования интереса мероприятия на основе интереса сообщества
//            $event->interests()->attach($interest->id, ['ratio' => $interest->pivot->ratio]);
//        }
    }
}
