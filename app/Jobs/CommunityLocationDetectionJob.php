<?php

namespace App\Jobs;

use App\Repositories\CommunityRepository;
use App\Services\ChatGPT\ChatGPTInteractionService;
use App\Services\FormatterService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

// Контекст основан на описании сообщества и последних записях (contextPosts)
// Запрос к GPT для получения местоположения в формате город, улица, дом (JSON)
class CommunityLocationDetectionJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public $communityId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(
        CommunityRepository $communityRepository,
        ChatGPTInteractionService $chatGPTService,
        FormatterService $formatterService,
    ): void {
        $community = $communityRepository->get($this->communityId);


        $prompt = view('prompts.community_location_extraction', [
            'community' => $community,
        ])->render();

        // Отправляем сообщение через сервис ChatGPT
        $response = $chatGPTService->sendMessage($prompt)['choices'][0]['message']['content'];
        $responseJson = $formatterService->formatToJson($response);

        if (!$responseJson) {
            Log::error('Ответ не был получен (CommunityLocationDetectionJob)');
            return;
        }

        $community->city = $responseJson->city === 'null' ? null : $responseJson->city;
        $community->street = $responseJson->street === 'null' ? null : $responseJson->street;
        $community->house = $responseJson->house === 'null' ? null : $responseJson->house;

        $community->save();
    }
}
