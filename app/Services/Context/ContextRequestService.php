<?php

namespace App\Services\Context;

use App\Contracts\Interfaces\ContextServiceInterface;
use App\Enums\ProcessStatusEnum;
use App\Jobs\ProcessContextJob;
use App\Models\ContextRequest;
use App\Models\ContextResponse;
use App\Services\ChatGPT\ChatGPTInteractionService;
use Exception;
use GuzzleHttp\Exception\ConnectException;

class ContextRequestService implements ContextServiceInterface
{
    public function __construct(
        protected ChatGPTInteractionService $chatGPTService
    ) {
    }

    /**
     * Продолжает обработку context-объекта
     *
     * @param int $contextId
     * @param string|null $contextClass
     * @return void
     * @throws Exception
     */
    public function processContext(int $contextId, ?string $contextClass = null): void
    {
        /** @var ContextRequest $contextRequest */
        $contextRequest = ContextRequest::query()->findOrFail($contextId);
        $contextRequest->update(['status' => ProcessStatusEnum::Pending->value]);

        try {
            // Отправляем сообщение через сервис ChatGPT
            $apiResponse = $this->chatGPTService->sendMessage($contextRequest->context);

            // Обновляем статус запроса
            $contextRequest->status = ProcessStatusEnum::Completed;
            $contextRequest->save();

            // Извлекаем ответ
            $messageContent = $apiResponse['choices'][0]['message']['content'] ?? '';
            $modelUsed = $apiResponse['model'] ?? '';

            // Сохраняем ответ
            $contextResponse = ContextResponse::query()->create([
                'context_id' => $contextRequest->id,
                'response' => $messageContent,
                'model' => $modelUsed,
            ]);
        } catch (ConnectException $exception) {
            // TODO: Логирование
            dump($exception->getMessage());
            return;
        } catch (Exception $exception) {
            $contextRequest->status = ProcessStatusEnum::Failed;
            $contextRequest->save();

            // TODO:
            throw $exception;
        }

        ProcessContextJob::dispatch($contextResponse->id, ContextResponse::class);
    }
}
