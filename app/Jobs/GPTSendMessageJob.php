<?php

namespace App\Jobs;

use App\Models\ContextRequest;
use App\Models\ContextResponse;
use App\Services\ChatGPT\ChatGPTInteractionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GPTSendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected $context,
        protected $contextRequestId,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(
        ChatGPTInteractionService $chatGPTService
    ): void
    {
        $contextRequest = ContextRequest::find($this->contextRequestId);

        if (!$contextRequest) {
            // Обработка ситуации, когда запись не найдена
            // TODO:
            throw new \Exception('Context Request Not Found');
        }

        try {
            // Отправляем сообщение через сервис ChatGPT
            $apiResponse = $chatGPTService->sendMessage($this->context);

            // Обновляем статус запроса
            $contextRequest->status = 'completed';
            $contextRequest->save();

            // Извлекаем ответ
            $messageContent = $apiResponse['choices'][0]['message']['content'] ?? '';
            $modelUsed = $apiResponse['model'] ?? '';

            // Сохраняем ответ
            ContextResponse::create([
                'context_id' => $contextRequest->id,
                'response' => $messageContent,
                'model' => $modelUsed,
            ]);
        } catch (\Exception $exception) {
            $contextRequest->status = 'failed';
            $contextRequest->save();

            // TODO:
            throw $exception;
        }
    }
}
