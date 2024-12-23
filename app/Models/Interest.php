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
class
Interest extends Model
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

    // Рекурсивное получение всех дочерних интересов
    public function getAllChildren()
    {
        $children = collect();

        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren());
        }

        return $children->unique('id');
    }

    // Рекурсивное получение всех родительских интересов
    public function getAllParents()
    {
        $parents = collect();

        foreach ($this->parent as $parent) {
            $parents->push($parent);
            $parents = $parents->merge($parent->getAllParents());
        }

        return $parents->unique('id');
    }

    // Получение полного списка интересов с учетом подчиненных и родителей
    public function getUnfoldedInterests()
    {
        $interests = collect([$this]);

        $parents = $this->getAllParents();
        $children = $this->getAllChildren();

        $interests = $interests->merge($parents);
        $interests = $interests->merge($children);

        return $interests->unique('id');
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
