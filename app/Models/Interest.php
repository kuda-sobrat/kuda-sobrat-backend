<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Интерес
 *
 * @property Interest|null $children
 * @property Interest|null $parent
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

    public function getLevelAttribute()
    {
        $level = 0;
        $current = $this;

//        dd($current->parent()->first());

        while ($current->parent()->first()) {
            $level++;
            $current = $current->parent()->first();
        }

        return $level;
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

    // Отношение к пользователям
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
