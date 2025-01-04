<?php

namespace App\Services\Context;

use App\Contracts\Interfaces\ContextServiceInterface;
use App\Enums\ProcessStatusEnum;
use App\Jobs\FetchEventInterestsFromGPTJob;
use App\Models\ContextEvent;
use App\Models\ContextPost;
use App\Models\Event;
use App\Models\EventAttachment;
use App\Models\EventGroup;
use App\Services\FormatterService;
use App\Services\Geocoder\GeocodingService;
use App\Support\Point;
use Illuminate\Support\Facades\Log;

class ContextEventService implements ContextServiceInterface
{
    public function __construct(
        public FormatterService $formatterService,
        protected GeocodingService $geocodingService,
    ) {
    }

    /**
     * Определяет геолокацию мероприятия, предварительно сравнивая с существующими записями
     *
     * @param ContextEvent $contextEvent
     * @return Point
     * @throws \App\Exceptions\GeocodingException
     */
    function getEventGroupLocation(ContextEvent $contextEvent) {
        // TODO: Кэширование запроса
        /** @var Event | null $event */
        $event = Event::query()->where('location_name', 'LIKE', "%{$contextEvent->location}%")->first();

        if (!empty($event)) {
            dump('$event->location:', $event->location);
            return $event->location;
        }
        dump('$event->location is null');

        $geocodingResponse = $this->geocodingService->geocode($this->formatterService->insertCity($contextEvent->location, $contextEvent->contextPost->community->city))[0];

        if (empty($geocodingResponse)) {
            throw new \Exception("Не удалось определить координаты для локации: $contextEvent->location");
        }

        return new Point(round($geocodingResponse->coordinates->latitude, 4), round($geocodingResponse->coordinates->longitude, 4));

    }

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
        /** @var ContextEvent $contextEvent */
        $contextEvent = ContextEvent::query()->findOrFail($contextId);

        if (empty($contextEvent->start_datetime)) {
            throw new \Exception("Не удалось спарсить мероприятие {$contextEvent->id}: время начала не определено");
        }

        // Устанавливаем статус Pending
        $contextEvent->update(['status' => ProcessStatusEnum::Pending->value]);

        try {
            $location = $this->getEventGroupLocation($contextEvent);

            dump("\nАдрес мероприятия: {$this->formatterService->insertCity($contextEvent->location, $contextEvent->contextPost->community->city)}\n Результат:\nlat:$location->latitude,\nlong:$location->longitude)\n");

            // Генерируем уникальный хэш
            $uniqueHash = md5(
                $contextEvent->community_id .
                $contextEvent->start_datetime->format('Y-m-d H:i:s') .
                $location->latitude .
                $location->longitude
            );

            /** @var ?Event $existingEvent */
            $existingEvent = Event::query()->where('unique_hash', $uniqueHash)->first();

            if ($existingEvent) {
                // Обновляем статус ContextEvent
                $contextEvent->status = ProcessStatusEnum::Completed->value;
                $contextEvent->save();

                // Связываем Event с ContextPost
                if ($contextEvent->contextPost) {
                    $contextEvent->contextPost->event_id = $existingEvent->id;
                    $contextEvent->contextPost->status = ProcessStatusEnum::Completed->value;
                    $contextEvent->contextPost->save();

                    // Копируем вложения из ContextPost в Event
                    $this->copyAttachments($contextEvent->contextPost, $existingEvent);
                }

                // Запускаем задачу FetchEventInterestsFromGPTJob
                FetchEventInterestsFromGPTJob::dispatch($contextEvent->contextPost->id);

                return;
            }

            // Создаем новый Event
            $event = new Event([
                'name' => $contextEvent->name,
                'description' => $contextEvent->description,
                'start_datetime' => $contextEvent->start_datetime,
                'end_datetime' => $contextEvent->end_datetime,
                'location' => $location,
                'location_name' => $contextEvent->location,
                'unique_hash' => $uniqueHash ?? null,
                'community_id' => $contextEvent->community_id,
                'event_group_id' => $contextEvent->event_group_id,
                // Дополнительные поля
            ]);

            $event->save();

            // Обновляем статус ContextEvent
            $contextEvent->status = ProcessStatusEnum::Completed->value;
            $contextEvent->save();

            // Связываем Event с ContextPost
            if ($contextEvent->contextPost) {
                $contextEvent->contextPost->event_id = $event->id;
                $contextEvent->contextPost->status = ProcessStatusEnum::Completed->value;
                $contextEvent->contextPost->save();

                // Копируем вложения из ContextPost в Event
                $this->copyAttachments($contextEvent->contextPost, $event);
            }

            // Запускаем задачу FetchEventInterestsFromGPTJob
            FetchEventInterestsFromGPTJob::dispatch($contextEvent->contextPost->id);

        } catch (\Exception $exception) {
            // Обновляем статус на Failed
            $contextEvent->update(['status' => ProcessStatusEnum::Failed->value]);

            if ($contextEvent->contextPost) {
                $contextEvent->contextPost->update(['status' => ProcessStatusEnum::Failed->value]);
            }

            Log::error("Ошибка в ContextEventService: " . $exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Копирует вложения из ContextPost в Event.
     *
     * @param ContextPost $contextPost
     * @param Event $event
     */
    protected function copyAttachments($contextPost, $event)
    {
        // Предполагаем, что у ContextPost есть метод 'attachments', возвращающий связанные модели ContextAttachment
        $contextAttachments = $contextPost->attachments;

        foreach ($contextAttachments as $contextAttachment) {
            // Создаем новое EventAttachment
            EventAttachment::create([
                'event_id' => $event->id,
                'type' => $contextAttachment->type,
                'url' => $contextAttachment->url,
                'title' => $contextAttachment->title,
                'text' => $contextAttachment->text,
            ]);
        }
    }
}
