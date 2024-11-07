<?php

namespace App\Console\Commands;

use App\Services\CommunityVerificationService;
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
    )
    {
        $communityVerificationService->verifyCommunities();
//        $response = $service->getWallPosts('redsuntheatre');
//        dd(array_keys($response['response']['items'][0]));
//        dd(array_keys($response['response']['items'][0]['attachments'][1]['link']));
//        dd($response['response']['items'][6]['attachments']);
//        dd($response['response']['items'][6]['text']);
    }
}
