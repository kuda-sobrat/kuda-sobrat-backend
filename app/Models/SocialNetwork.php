<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Социальная сеть
 */
class SocialNetwork extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function communities()
    {
        return $this->belongsToMany(Community::class, 'community_social_links');
    }
}
