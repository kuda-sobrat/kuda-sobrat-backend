<?php

namespace App\Console\Commands;

use App\Models\ContextResponse;
use App\Services\CommunityVerificationService;
use App\Services\Context\ContextService;
use App\Services\EventService;
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
//        $communityVerificationService->verifyCommunities();
//
//        /** @var ContextPost $post */
//        $post = ContextPost::query()->find(187);
//        ProcessContextJob::dispatch($post->id);

        /** @var ContextResponse $contextResponse */
        $contextResponse = ContextResponse::query()->first();

//        $eventService->processContext($contextResponse);
    }
}
