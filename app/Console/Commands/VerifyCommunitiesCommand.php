<?php

namespace App\Console\Commands;

use App\Services\CommunityVerificationService;
use Illuminate\Console\Command;

class VerifyCommunitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'communities:verification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Верифицирует все сохраненные сообщества';

    /**
     * Execute the console command.
     */
    public function handle(
        CommunityVerificationService $communityVerificationService,
    ) {
        $communityVerificationService->verifyCommunities();
    }
}
