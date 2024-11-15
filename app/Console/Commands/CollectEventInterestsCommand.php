<?php

namespace App\Console\Commands;

use App\Contracts\Interfaces\EventRepositoryInterface;
use App\Jobs\FetchEventInterestsFromGPTJob;
use Illuminate\Console\Command;

class CollectEventInterestsCommand extends Command
{
    /** @var string $signature */
    protected $signature = 'events:collect-interests';

    /** @var string $description */
    protected $description = 'Формирует интересы мероприятий на основе информации о сообществе и описания';

    /**
     * Execute the console command.
     */
    public function handle(
        EventRepositoryInterface $eventRepository,
    )
    {
        $events = $eventRepository->getAll();
        foreach ($events as $event) {
            foreach ($event->contextPosts as $contextPost) {
                FetchEventInterestsFromGPTJob::dispatch($contextPost->id);
            }
        }
    }
}
