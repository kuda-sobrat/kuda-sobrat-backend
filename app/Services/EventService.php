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

    }
}
