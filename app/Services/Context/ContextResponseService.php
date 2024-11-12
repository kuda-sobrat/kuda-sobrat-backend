<?php

namespace App\Services\Context;

use App\Contracts\Interfaces\ContextServiceInterface;
use App\Enums\ProcessStatusEnum;
use App\Jobs\GenerateEventInterestsJob;
use App\Models\ContextResponse;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

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

        try {
            $response = json_decode($contextResponse->response);
        } catch (\Exception $e) {
            $contextResponse->update(['status' => ProcessStatusEnum::Failed->value]);
            // TODO: Обработка ошибок, логирование
            throw $e;
        }

        if (empty($response)) {
            $contextResponse->update(['status' => ProcessStatusEnum::Failed->value]);
            // TODO: Логирование
//            throw new \Exception('Тело ответа пусто');
            return;
        } else if (!$response->is_event) {
            $contextResponse->update(['status' => ProcessStatusEnum::Completed->value]);
            return;
        }

        foreach ($response->events as $eventData) {
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
                $contextResponse->contextPost->save();
            });

            // TODO: Вынести в обработчик события завершения формирования event
            GenerateEventInterestsJob::dispatch($event->id);
        }

        $contextResponse->update(['status' => ProcessStatusEnum::Completed->value]);
        // TODO: отправить событие о завершении формирования event
    }
}
