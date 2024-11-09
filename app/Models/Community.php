<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Сообщество, группа в соц. сети.
 *
 * @property $name
 * @property $description
 * @property $last_checked_at
 * @property $is_verified
 * @property SocialNetwork $socialNetworks социальная сеть
 */
class Community extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'last_checked_at',
        'is_verified',
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
}
