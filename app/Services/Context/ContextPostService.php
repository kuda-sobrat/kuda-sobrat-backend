<?php

namespace App\Services\Context;

use App\Contracts\Interfaces\ContextServiceInterface;
use App\Enums\ProcessStatusEnum;
use App\Jobs\ProcessContextJob;
use App\Models\ContextPost;
use App\Models\ContextRequest;
use Illuminate\Support\Facades\DB;

class ContextPostService implements ContextServiceInterface
{
    public function __construct(
      protected ContextService $contextService,
    ) {
    }

    /**
     * Продолжает обработку context-объекта. Формирует и сохраняет запрос и ответ к chatGTP
     *
     * @param int $contextId
     * @param string|null $contextClass
     * @return void
     * @throws \Exception
     */
    public function processContext(int $contextId, ?string $contextClass = null): void
    {
        $type = 'setup_event';

        /** @var ContextPost $contextPost */
        $contextPost = ContextPost::query()->findOrFail($contextId);

        $contextPost->update(['status' => ProcessStatusEnum::Pending->value]);

        $context = $this->contextService->buildContext($contextPost);

        $contextPost->processed_text = $context;
        $contextPost->save();

        $prompt = view('prompts.event_extraction', [
            'nowYear' => now()->year,
            'nowMonth' => now()->month,
            'inputText' => $context,
        ])->render();

        $contextRequest = new ContextRequest();
        DB::transaction(function () use ($prompt, $type, &$contextRequest, $contextPost) {
            /** @var ContextRequest $contextRequest */
            $contextRequest = ContextRequest::query()->create([
                'type' => $type,
                'context' => $prompt,
                'context_id' => $contextPost->id,
                'status' => 'pending',
            ]);
        });

        ProcessContextJob::dispatch($contextRequest->id, ContextRequest::class);
    }
}
