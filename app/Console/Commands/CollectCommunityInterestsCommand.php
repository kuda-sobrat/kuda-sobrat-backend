<?php

namespace App\Console\Commands;

use App\Events\CommunityVerifiedEvent;
use App\Repositories\CommunityRepository;
use Illuminate\Console\Command;

class CollectCommunityInterestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'communities:collect-interests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Собирает интересы верифицированных сообществ';

    /**
     * Execute the console command.
     */
    public function handle(
        CommunityRepository $communityRepository
    )
    {
        $communities = $communityRepository->getVerifiedCommunities();
        foreach ($communities as $community) {
            CommunityVerifiedEvent::dispatch($community->id);
        }
    }
}
