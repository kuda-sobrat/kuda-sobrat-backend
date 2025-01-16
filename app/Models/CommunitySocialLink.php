<?php

namespace App\Models;

use App\Services\SocialMedia\SocialMediaApiFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Ramsey\Collection\Collection;

/**
 * Ссылка сообщества на соц.сеть
 *
 * @property $community_id
 * @property $social_network_id
 * @property $social_network_community_id
 * @property $path
 * @property SocialNetwork $socialNetwork
 * @property Collection<EventSource> $eventSources
 */
class CommunitySocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
      'community_id',
      'social_network_id',
      'social_network_community_id',
      'path',
    ];

    protected $appends = [
        'generated_link'
    ];

    public function socialNetwork()
    {
        return $this->belongsTo(SocialNetwork::class, 'social_network_id', 'id');
    }

    /**
     * Связанные ссылки на мероприятия
     *
     * @return HasMany
     */
    public function eventSources(): HasMany
    {
        return $this->hasMany(EventSource::class);
    }

    /**
     * Генерирует и возвращает ссылку для сообщества на основе социальной сети.
     *
     * @return string|null
     */
    public function getGeneratedLinkAttribute(): ?string
    {
        try {
            if ($this && $this->socialNetwork) {
                // Получаем сервис социальной сети
                $socialMediaService = SocialMediaApiFactory::getService($this->socialNetwork->name);
                if (method_exists($socialMediaService, 'generateCommunityLink')) {
                    // Генерируем ссылку для сообщества
                    return $socialMediaService->generateCommunityLink($this->social_network_community_id);
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } catch (\Exception $exception) {
            // TODO: Обработка telegram
//            Log::error('getGeneratedLinkAttribute:' . $exception->getMessage());
        }
        return null;
    }
}
