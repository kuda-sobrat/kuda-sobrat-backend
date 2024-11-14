<?php

namespace App\Services;

use App\Models\ContextResponse;
use App\Services\ChatGPT\ChatGPTInteractionService;

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

    }
}
