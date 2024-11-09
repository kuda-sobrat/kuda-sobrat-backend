<?php

namespace App\Services;

use App\Jobs\GenerateEventInterestsJob;
use App\Models\ContextResponse;
use App\Models\Event;
use App\Services\ChatGPT\ChatGPTInteractionService;
use Illuminate\Support\Facades\DB;

/**
 * Сервис для работы с мероприятиями (событиями)
 */
class EventService
{
    public function __construct(
        protected ChatGPTInteractionService $chatGPTService,
    ) {
    }

    /**
     * Продолжает обработку context-объекта. Формирует информацию о мероприятии
     * TODO: Вынести в отдельный сервис? (process...)
     *
     * @param ContextResponse $context
     * @return void
     */
    public function processContext(ContextResponse $context)
    {
        try {
            $response = json_decode($context->response);
        } catch (\Exception $e) {
            // TODO: Обработка ошибок
            throw $e;
        }

        if (!$response->is_event) {
            return;
        }

        foreach ($response->events as $eventData) {
            DB::transaction(function () use ($eventData, &$context) {
                /** @var Event $event */
                $event = Event::query()->make([
                    'community_id' => $context->contextPost->socialLink->community_id,
                    'name' => $eventData->name,
                    'description' => $eventData->description,
                    'start_datetime' => new \DateTime($eventData->start_datetime),
                    'end_datetime' => new \DateTime($eventData->end_datetime),
                    'location' => $eventData->location,
                ]);

                $event->setupUniqueHash();

                $event->findOrSave();

                $context->contextPost->event_id = $event->id;
                $context->contextPost->save();

                GenerateEventInterestsJob::dispatch($event->id);
            });

        }
    }
}
