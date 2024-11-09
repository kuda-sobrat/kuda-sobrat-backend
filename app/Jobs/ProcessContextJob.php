<?php

namespace App\Jobs;

use App\Models\ContextPost;
use App\Services\ContextService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessContextJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $contextId,
    ) {
    }

    /**
     * @param ContextService $contextService
     * @return void
     * @throws \Exception
     */
    public function handle(ContextService $contextService): void
    {
        /** @var ContextPost $contextPost */
        $contextPost = ContextPost::query()->find($this->contextId);

        if (!$contextPost) {
            // TODO:
            throw new \Exception('Context Post not found');
        }

        $context = $contextService->buildContext($contextPost);

        $contextPost->processed_text = $context;
        $contextPost->save();

        $prompt = view('prompts.event_extraction', [
            'nowYear' => now()->year,
            'nowMonth' => now()->month,
            'inputText' => $context,
        ])->render();

        // Формирую запрос на определение

        $contextService->processContext($prompt, $contextPost->id);


//        // Обновляем запись контента в базе данных
//        $content = \App\Models\ContextPost::find($this->contextId);
//
//        dd($content);
//
//        if ($content) {
//            $content->processed_text = $processedData['processed_text'];
//            $content->is_event = $processedData['is_event'];
//            $content->event_fields = $processedData['event_fields'];
//            $content->save();
//        } else {
//            // Обработка ситуации, когда контент не найден
//            \Log::error("Content not found with ID: " . $this->contentId);
//        }
    }
}
