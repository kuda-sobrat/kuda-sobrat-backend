<?php

namespace App\Jobs;

use App\Enums\ProcessStatusEnum;
use App\Models\ContextPost;
use App\Models\ContextRequest;
use App\Repositories\ContextPostsRepository;
use App\Services\Context\ContextPostService;
use App\Services\InterestService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FetchEventInterestsFromGPTJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param $contextPostId
     */
    public function __construct(
        protected $contextPostId,
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        InterestService $interestService,
        ContextPostService $contextPostService,
    )
    {
        $type = 'interests_comparison';

        /** @var ContextPost $contextPost */
        $contextPost = ContextPost::query()->find($this->contextPostId);

        $contextPost->event->status = ProcessStatusEnum::Pending;
        $contextPost->event->save();

        if (!$contextPost || !$contextPost->event) {
            // TODO: Логирование
            Log::error('Пост или мероприятие не найдено');
        }

        $interests = implode(',', $interestService->getBaseInterests()->map(function ($interest) use ($contextPost) {
            return "$interest->name[$interest->id]";
        })->toArray());

        $prompt = view('prompts.interests_comparison', [
            'interests' => $interests,
            'context' => $contextPost->text,
            'geolocation' => $contextPost->event->location,
        ])->render();

        $contextPostService->sendPrompt($contextPost, $prompt, $type, true);

        $request = $contextPostService->getLastRequest($contextPost, $type);
        $contextPost->event->interests()->syncWithoutDetaching($request->contextResponse->jsonResponse);

    }
}
