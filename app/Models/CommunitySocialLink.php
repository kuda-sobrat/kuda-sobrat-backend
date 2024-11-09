<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Ссылка сообщества на соц.сеть
 *
 * @property $community_id
 * @property $social_network_id
 * @property $social_network_community_id
 * @property $path
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
}
