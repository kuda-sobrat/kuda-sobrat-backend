<?php

namespace App\Jobs;

use App\Models\Community;
use App\Repositories\CommunityRepository;
use App\Repositories\InterestRepository;
use App\Services\ChatGPT\ChatGPTInteractionService;
use App\Services\FormatterService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchCommunityInterestsFromGPTJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param $contextPostId
     */
    public function __construct(
        protected $communityId,
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle(
        InterestRepository $interestRepository,
        ChatGPTInteractionService $chatGPTService,
        CommunityRepository $communityRepository,
        FormatterService $formatterService,
    )
    {
        /** @var Community $community */
        $community = $communityRepository->get($this->communityId);

        if (!$community) {
            // TODO: Логирование
            Log::error('Пост или мероприятие не найдено');
        }

        $contextPostsWithEvents = $communityRepository->getContextPostsWithEvents($community);
        $context = implode("\n", $contextPostsWithEvents->pluck('event.description')->map(function ($item, $key) {
            return "$key->$item";
        })->toArray());

        $interests = implode(',', $interestRepository->getInterestsByLevel(1)->map(function ($interest) {
            return "$interest->name[$interest->id]";
        })->toArray());

        $prompt = view('prompts.community_interests_extraction', [
            'interests' => $interests,
            'context' => $context,
//            'geolocation' => $community->location, // TODO: geolocation (письмо)
            'description' => $community->description,
        ])->render();

        $times = 3;
        while ($times > 0) {
            $response = $chatGPTService->sendMessage($prompt)['choices'][0]['message']['content'];
            $responseJson = $formatterService->formatToJson($response);

            if (!$responseJson) {
                Log::error('Ответ не был получен (FetchCommunityInterestsFromGPTJob)');
                return;
            }

            $community->interests()->syncWithoutDetaching($responseJson);
            $times--;
        }
    }
}
