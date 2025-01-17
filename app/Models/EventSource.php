<?php

namespace App\Models;

use App\Services\SocialMedia\SocialMediaApiFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $event_id - ID мероприятия
 * @property int $social_link_id - ID социальной сети
 * @property int $source_id - ID поста источника
 * @property string $generated_link - Соответствующая ссылка
 * @property Event $event
 * @property CommunitySocialLink $socialLink
 */
class EventSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'social_link_id',
        'source_id',
        'generated_link',
    ];

    public $timestamps = false;

    /**
     * Связанное мероприятие
     *
     * @return BelongsTo
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Связанная социальная сеть
     *
     * @return BelongsTo
     */
    public function socialLink(): BelongsTo
    {
        return $this->belongsTo(CommunitySocialLink::class);
    }

    /**
     * Добавляем событие модели
     */
    protected static function booted()
    {
        $generateLink = function ($eventSource): void
        {
            /** @var EventSource $eventSource */
            $event = $eventSource->event;
            $sourceId = $eventSource->source_id;
            $socialLink = $eventSource->socialLink;

            try {
                if ($sourceId && $event && $socialLink->socialNetwork) {
                    // Получаем сервис социальной сети
                    $socialMediaService = SocialMediaApiFactory::getService($socialLink->socialNetwork->name);

                    if (method_exists($socialMediaService, 'generateEventLink')) {
                        // Генерируем ссылку
                        $eventSource->generated_link = $socialMediaService->generateEventLink($sourceId, $socialLink->social_network_community_id);
                    } else {
                        $eventSource->generated_link = null;
                    }
                } else {
                    $eventSource->generated_link = null;
                }
            } catch (\Exception $e) {
                dump($e->getMessage());
            }
        };

        static::creating($generateLink);
        static::updating($generateLink);
    }
}
