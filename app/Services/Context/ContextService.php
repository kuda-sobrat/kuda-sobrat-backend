<?php

namespace App\Services\Context;

use App\Contracts\Interfaces\ContextServiceInterface;
use App\Models\ContextPost;
use App\Models\ContextRequest;
use App\Models\ContextResponse;
use App\Services\ChatGPT\ChatGPTInteractionService;
use Exception;
use Illuminate\Support\Facades\App;

/**
 * Сервис для работы с контекстом
 */
class ContextService implements ContextServiceInterface
{
    public function __construct(
        protected ChatGPTInteractionService $chatGPTService,
    ) {
    }

    /**
     * Продолжает обработку context-объекта
     *
     * @param int $contextId
     * @param string|null $contextClass
     * @return void
     * @throws Exception
     */
    public function processContext(int $contextId, string|null $contextClass = ContextPost::class): void
    {
        $serviceMap = [
            ContextPost::class => ContextPostService::class,
            ContextRequest::class => ContextRequestService::class,
            ContextResponse::class => ContextResponseService::class,
        ];

        if (!isset($serviceMap[$contextClass])) {
            throw new \Exception('Класс контекста не найден. Класс: ' . $contextClass);
        }

        $instance = App::make($serviceMap[$contextClass]);
        $instance->processContext($contextId, $contextClass);
    }

    /**
     * Формирует контекст для запроса
     * TODO: Пересмотреть на использование шаблона blade
     *
     * @param ContextPost $post
     * @return string
     */
    public function buildContext(ContextPost $post): string
    {
        $attachmentsTmp = [];

        $post->attachments->map(function ($attachment) use (&$attachmentsTmp) {
            switch ($attachment->type) {
                case 'photo':
//                    $attachmentsTmp['Фотографии:'][] = '- фотография';
                    $attachmentsTmp['Фотографии:'][] = '- <пусто>';
                    break;
                case 'link':
                    $attachmentsTmp['Ссылки:'][] = "- $attachment->title - $attachment->text";
                    break;
                default:
                    $attachmentsTmp[$attachment->type][] = "- $attachment->text";
            }
        });

        $attachmentsText = "\nПриложения:\n";
        foreach ($attachmentsTmp as $type => $attachments) {
            $attachmentsText = "$attachmentsText## $type\n" . implode(";\n", $attachments);
        }

        $context = $post->text . "\n\n";
        $context .= "Текстовая информация приложений:\n\n";
        $context .= $attachmentsText;

        return $context;
    }
}
