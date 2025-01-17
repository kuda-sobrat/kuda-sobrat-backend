<?php

namespace App\Models;

use App\Enums\ProcessStatusEnum;
use App\Services\SocialMedia\SocialMediaApiFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Сообщество, группа в соц. сети.
 *
 * @property $id
 * @property $name
 * @property $verification_status
 * @property $description
 * @property $last_checked_at
 * @property $is_verified
 * @property $city
 * @property $street
 * @property $house
 * @property Collection<SocialNetwork> $socialNetworks Социальные сети
 * @property Collection<CommunitySocialLink> $socialLinks Ссылки на соц. сети
 * @property Collection<ContextPost> $contextPosts Посты
 * @property Collection<Interest> $interests Интересы сообщества
 * @property Collection<Event> $events Мероприятия связанные с сообществом
 */
class Community extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'verification_status',
        'description',
        'last_checked_at',
        'is_verified',
    ];

    protected $casts = [
        'verification_status' => ProcessStatusEnum::class,
        'last_checked_at' => 'datetime',
    ];

    /**
     * Отношение к социальной сети.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function socialNetworks()
    {
        return $this->belongsToMany(SocialNetwork::class, 'community_social_links')
            ->withPivot('social_network_community_id', 'path');
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'community_interest');
    }

    public function socialLinks()
    {
        return $this->hasMany(CommunitySocialLink::class, 'community_id', 'id');
    }

    /**
     * @param SocialNetwork $socialNetwork
     * @return null|CommunitySocialLink
     */
    public function socialLinkBySocialNetwork(SocialNetwork $socialNetwork)
    {
        return $this->socialLinks()->where('social_network_id', '=', $socialNetwork->id)->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function contextPosts()
    {
        return $this->hasManyThrough(ContextPost::class, CommunitySocialLink::class, 'community_id', 'social_link_id', 'id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function events()
    {
        return $this->belongsToMany(Event::class);
    }
}
