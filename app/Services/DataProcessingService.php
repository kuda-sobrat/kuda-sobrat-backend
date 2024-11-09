<?php

namespace App\Services;

use App\Models\ContextResponse;
use App\Models\Event;

class DataProcessingService
{
    public function processResponse(ContextResponse $contextResponse)
    {
        $data = json_decode($contextResponse->response, true);

        if (isset($data['is_exists']) && !$data['is_exists']) {
            // Обновляем статус поста на 'no_event'
            $post = $contextResponse->contextRequest->post;
            $post->status = 'no_event';
            $post->save();
            return;
        }

        // Если мероприятие найдено, сохраняем его
        $eventData = $this->extractEventData($data);
        $event = new Event($eventData);
        $event->save();

        // Обновляем пост
        $post = $contextResponse->contextRequest->post;
        $post->event_id = $event->id;
        $post->status = 'processed';
        $post->save();
    }

    protected function extractEventData($data)
    {
        // Извлекаем необходимые поля из данных
        return [
            'title' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'start_datetime' => $data['start_datetime'] ?? null,
            'end_datetime' => $data['end_datetime'] ?? null,
            'location' => $data['location'] ?? '',
            'unique_hash' => md5($data['title'] . $data['start_datetime'] . $data['location']),
            // Другие поля...
        ];
    }
}
