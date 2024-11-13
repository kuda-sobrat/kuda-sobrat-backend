<?php

namespace App\Services\Context;

use App\Contracts\Interfaces\ContextServiceInterface;
use App\Enums\ProcessStatusEnum;
use App\Jobs\GenerateEventInterestsJob;
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

        $contextResponse->update(['status' => ProcessStatusEnum::Pending->value]);

        $response = $contextResponse->jsonResponse;

        if (empty($response)) {
            $contextResponse->update(['status' => ProcessStatusEnum::Failed->value]);
            // TODO: Логирование
            Log::info("Тело отвате пусто ContextPost ID: ${$contextId}");
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

                // TODO: Вынести в обработчик события завершения формирования event
                GenerateEventInterestsJob::dispatch($event->id);
            } catch (\Exception $exception) {
                $contextResponse->update(['status' => ProcessStatusEnum::Failed->value]);
                $contextResponse->contextPost->update(['status' => ProcessStatusEnum::Failed->value]);
                Log::error($exception->getMessage());
            }
        }

        $contextResponse->update(['status' => ProcessStatusEnum::Completed->value]);
        // TODO: отправить событие о завершении формирования event
    }
}
