<?php

namespace App\Console\Commands;

use App\Enums\ProcessStatusEnum;
use App\Models\ContextEvent;
use App\Models\ContextPost;
use App\Models\Interest;
use App\Services\Context\ContextService;
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
        EventService $service,
        ContextService $contextService,
    )
    {
        /** @var ContextPost $contextPost */
        $contextPost = ContextPost::query()->first();

        $prompt = view('prompts.event_extraction', [
            'date' => $contextPost->created_at,
            'inputText' => $contextService->buildContext($contextPost),
            'communityLocation' => "\nгород: {$contextPost->community->city}\nулица: {$contextPost->community->street}\nдом: {$contextPost->community->house}"
        ])->render();

        dd($prompt);
//        dd(collect($events->items()));
    }
}
