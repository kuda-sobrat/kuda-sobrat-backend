<?php

namespace App\Console\Commands;

use App\Jobs\ProcessContextJob;
use App\Models\ContextPost;
use App\Models\ContextResponse;
use App\Models\Event;
use App\Services\CommunityVerificationService;
use App\Services\ContextService;
use App\Services\EventService;
use Carbon\Carbon;
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
        EventService $eventService,
    )
    {
        /** @var ContextResponse $contextResponse */
        $contextResponse = ContextResponse::query()->first();

        $eventService->processContext($contextResponse);

//        $event = new Event();
//        $event->name = 'Test';
//        $event->description = 'TestDescription';
//        $event->community_id = 1;
//        $event->start_datetime = Carbon::make('2024-11-09 10:00:00');
//        $event->location = 'Минская 51, кв. 4';
//
//        $event->findOrSave();
//        $event->save();
//        dd($event);

//        /** @var ContextPost $post */
//        $post = ContextPost::query()->find(187);

//        ProcessContextJob::dispatch($post->id);
//        $communityVerificationService->verifyCommunities();
//        $response = $service->getWallPosts('redsuntheatre');
//        dd(array_keys($response['response']['items'][0]));
//        dd(array_keys($response['response']['items'][0]['attachments'][1]['link']));
//        dd($response['response']['items'][6]['attachments']);
//        dd($response['response']['items'][6]['text']);
    }
}
