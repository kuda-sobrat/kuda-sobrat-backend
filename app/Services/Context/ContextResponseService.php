<?php

namespace App\Services\Context;

use App\Contracts\Interfaces\ContextServiceInterface;
use App\Enums\EventTypeEnum;
use App\Enums\ProcessStatusEnum;
use App\Jobs\ProcessContextJob;
use App\Models\ContextEvent;
use App\Models\ContextResponse;
use App\Models\EventGroup;
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

        $eventGroup = null;

        foreach ($response->events as $eventData) {
            try {
                if (empty($eventData->location)) {
                    Log::warning("Отсутствует location для ContextResponse ID: $contextId");
                    continue;
                }

                // Генерируем уникальный хэш
                $uniqueHash = hash('sha256', $contextResponse->contextPost->socialLink->community_id . $eventData->location . $eventData->start_datetime);

                // Проверяем, существует ли запись с таким же хэшем
                $existingEvent = ContextEvent::query()->where('unique_hash', $uniqueHash)->first();

                if ($existingEvent) {
                    // Запись уже существует, выводим предупреждение и пропускаем сохранение
                    Log::warning("ContextEvent с уникальным хэшем {$uniqueHash} уже существует. Обработка продолжена");
                    continue;
                }

                /** @var ContextEvent $contextEvent */
                $contextEvent = ContextEvent::query()->make([
                    'unique_hash' => $uniqueHash,
                    'community_id' => $contextResponse->contextPost->socialLink->community_id,
                    'context_id' => $contextResponse->id,
                    'name' => $eventData->name,
                    'description' => $eventData->description,
                    'start_datetime' => !empty($eventData->start_datetime) ? new \DateTime($eventData->start_datetime) : null,
                    'end_datetime' => !empty($eventData->end_datetime) ? new \DateTime($eventData->end_datetime) : null,
                    'location' => $eventData->location,
                    'cost' => $eventData->price,
                    'type' => !empty($eventData->type) ? EventTypeEnum::from($eventData->type) : null,
                ]);

                if (empty($eventGroup)) {
                    $eventGroup = EventGroup::query()->create([
                        'name' => $eventData->name,
                        'description' => $contextResponse->contextPost->text,
                    ]);
                }

                $contextEvent->event_group_id = $eventGroup->id;

                $contextEvent->save();
                $contextResponse->update(['status' => ProcessStatusEnum::Completed->value]);

                ProcessContextJob::dispatch($contextEvent->id, ContextEvent::class);
            } catch (\Exception $exception) {
                dump($exception->getMessage());
                $contextResponse->update(['status' => ProcessStatusEnum::Failed->value]);
                $contextResponse->contextPost->update(['status' => ProcessStatusEnum::Failed->value]);
                Log::error($exception->getMessage());
            }
        }
        $contextResponse->update(['status' => ProcessStatusEnum::Completed->value]);
    }
}
