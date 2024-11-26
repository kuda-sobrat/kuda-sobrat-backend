<?php

namespace App\Services\Context;

use App\Contracts\Interfaces\ContextServiceInterface;
use App\Enums\ProcessStatusEnum;
use App\Jobs\FetchEventInterestsFromGPTJob;
use App\Models\ContextResponse;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContextResponseService implements ContextServiceInterface
{

    /**
     * Продолжает обработку context-объекта
     *
     * @param int $contextId
     * @param string|null $contextClass
     * @return void
     * @throws \Exception
     */
    public function processContext(int $contextId, ?string $contextClass = null): void
    {
        /** @var ContextResponse $contextResponse */
        $contextResponse = ContextResponse::query()->findOrFail($contextId);

        if ($contextResponse->contextRequest->type !== 'setup_event') {
            Log::info("Обработка ContextResponseService: вызван {$contextResponse->contextRequest->type}");
            return;
        }

        $contextResponse->update(['status' => ProcessStatusEnum::Pending->value]);

        $response = $contextResponse->jsonResponse;

        if (empty($response)) {
            $contextResponse->update(['status' => ProcessStatusEnum::Failed->value]);
            dump($contextResponse);
            // TODO: Логирование
            Log::info("Тело ответа пусто ContextPost ID: $contextId");
            return;
        } else if (!$response->is_event) {
            $contextResponse->update(['status' => ProcessStatusEnum::Completed->value]);
            return;
        }

        foreach ($response->events as $eventData) {
            try {
                if (empty($eventData->location)) {
                    // TODO: Логирование
                    continue;
                }

                /** @var Event $event */
                $event = Event::query()->make([
                    'community_id' => $contextResponse->contextPost->socialLink->community_id,
                    'name' => $eventData->name,
                    'description' => $eventData->description,
                    'start_datetime' => new \DateTime($eventData->start_datetime),
                    'end_datetime' => new \DateTime($eventData->end_datetime),
                    'location' => $eventData->location,
                ]);

                DB::transaction(function () use ($eventData, &$contextResponse, &$event) {
                    $event->setupUniqueHash();

                    $event->findOrSave();

                    $contextResponse->contextPost->event_id = $event->id;
                    $contextResponse->contextPost->status = ProcessStatusEnum::Completed;
                    $contextResponse->contextPost->save();
                });
                $contextResponse->update(['status' => ProcessStatusEnum::Completed->value]);
                FetchEventInterestsFromGPTJob::dispatch($contextResponse->contextPost->id);
            } catch (\Exception $exception) {
                $contextResponse->update(['status' => ProcessStatusEnum::Failed->value]);
                $contextResponse->contextPost->update(['status' => ProcessStatusEnum::Failed->value]);
                Log::error($exception->getMessage());
            }
        }

        $contextResponse->update(['status' => ProcessStatusEnum::Completed->value]);
    }
}
