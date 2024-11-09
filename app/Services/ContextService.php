<?php

namespace App\Services;

use App\Jobs\GPTSendMessageJob;
use App\Models\ContextPost;
use App\Models\ContextRequest;
use App\Services\ChatGPT\ChatGPTInteractionService;
use Illuminate\Support\Facades\DB;

class ContextService
{
    public function __construct(
        protected ChatGPTInteractionService $chatGPTService,
    ) {
    }

    public function processContext(string $prompt, int $contextId, string $type = 'none')
    {
        $contextRequest = new ContextRequest();
        DB::transaction(function () use ($prompt, $type, &$contextRequest, $contextId) {
            /** @var ContextRequest $contextRequest */
            $contextRequest = ContextRequest::query()->create([
                'type' => $type,
                'context' => $prompt,
                'context_id' => $contextId,
                'status' => 'pending',
            ]);
        });

        GPTSendMessageJob::dispatch($prompt, $contextRequest->id);
    }

    /**
     * @param ContextPost $post
     * @return string
     */
    public function buildContext(ContextPost $post): string
    {
        $attachmentsTmp = [];

        $post->attachments->map(function ($attachment) use (&$attachmentsTmp) {
            switch ($attachment->type) {
                case 'photo':
//                    $attachmentsTmp['Фотографии:'][] = '- фотография';
                    $attachmentsTmp['Фотографии:'][] = '- <пусто>';
                    break;
                case 'link':
                    $attachmentsTmp['Ссылки:'][] = "- $attachment->title - $attachment->text";
                    break;
                default:
                    $attachmentsTmp[$attachment->type][] = "- $attachment->text";
            }
        });

        $attachmentsText = "\n\n### Приложения:\n";
        foreach ($attachmentsTmp as $type => $attachments) {
            $attachmentsText = "$attachmentsText## $type\n" . implode(";\n", $attachments);
        }

        $context = "### Пост из социальной сети:\n\n";
        $context .= $post->text . "\n\n";
        $context .= "Текстовая информация приложений:\n\n";
        $context .= $attachmentsText;

        return $context;
    }
}
