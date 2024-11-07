<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Сообщество, группа в соц. сети.
 *
 * @property SocialNetwork $socialNetworks социальная сеть
 */
class Community extends Model
{
    use HasFactory;

    public $timestamps = false;

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

    public function contextPosts()
    {
        return $this->hasMany(ContextPost::class);
    }
}
