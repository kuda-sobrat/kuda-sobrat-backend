<?php

namespace App\Console\Commands;

use App\Jobs\ProcessContextJob;
use App\Models\ContextPost;
use App\Services\CommunityVerificationService;
use App\Services\ContextService;
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
    )
    {
        $post = ContextPost::query()->find(7);

        // 1. Преобразовать text в processed_text (вмещающий ссылки и прочие вложения)
        // 2. Отправить запрос на проверку явялется ли пост мероприятием
        // + (если это мероприяти(-е/-я) выделить главные поля)

        ProcessContextJob::dispatch($post->id);
//        $communityVerificationService->verifyCommunities();
//        $response = $service->getWallPosts('redsuntheatre');
//        dd(array_keys($response['response']['items'][0]));
//        dd(array_keys($response['response']['items'][0]['attachments'][1]['link']));
//        dd($response['response']['items'][6]['attachments']);
//        dd($response['response']['items'][6]['text']);
    }
}
