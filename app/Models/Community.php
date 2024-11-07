<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Сообщество, группа в соц. сети
 */
class Community extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function socialNetworks()
    {
        return $this->belongsToMany(SocialNetwork::class, 'community_social_links');
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
