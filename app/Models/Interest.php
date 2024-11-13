<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Интерес
 */
class Interest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name', 'description', 'is_paid'];

    // Отношение дочерних интересов
    public function children()
    {
        return $this->belongsToMany(Interest::class, 'interest_relations', 'parent_interest_id', 'interest_id');
    }

    // Отношение родительского интереса
    public function parent()
    {
        return $this->belongsToMany(Interest::class, 'interest_relations', 'interest_id', 'parent_interest_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_interest');
    }

    public function contextPosts()
    {
        return $this->belongsToMany(ContextPost::class, 'context_post_interest');
    }

    public function communities()
    {
        return $this->belongsToMany(Community::class, 'community_interest');
    }
}
