<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Социальная сеть
 *
 * @property $name
 * @property $base_url
 */
class SocialNetwork extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'base_url',
    ];

    public function communities()
    {
        return $this->belongsToMany(Community::class, 'community_social_links');
    }
}
