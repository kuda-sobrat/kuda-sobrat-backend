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
            'date' => $contextPost->created_at,
            'inputText' => $context,
            'communityLocation' => "\nгород: {$contextPost->community->city}\nулица: {$contextPost->community->street}\nдом: {$contextPost->community->house}"
        ])->render();

        $this->sendPrompt($contextPost, $prompt, $type);
    }

    /**
     * Отправляет prompt к GPT. Результат связывает с ContextPost
     *
     * @param ContextPost $contextPost
     * @param string $prompt
     * @param string $type
     * @param bool $sync
     * @return void
     */
    public function sendPrompt(ContextPost $contextPost, string $prompt, string $type, bool $sync = false): void
    {
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

        if ($sync) {
            ProcessContextJob::dispatchSync($contextRequest->id, ContextRequest::class);
        } else {
            ProcessContextJob::dispatch($contextRequest->id, ContextRequest::class);
        }
    }

    /**
     * Получает последний запрос. По типу, если указан тип
     *
     * @param ContextPost $contextPost
     * @param $type
     * @return ContextRequest
     */
    public function getLastRequest(ContextPost $contextPost, $type = null): ContextRequest
    {
        /** @var ContextRequest|null $result */
        $result = !$type ?
            ContextRequest::query()->where('context_id', $contextPost->id)->orderByDesc('created_at')->first()
            : ContextRequest::query()->where('type', '=', $type)->where('context_id', $contextPost->id)->orderByDesc('created_at')->first();

        return $result;
    }
}
