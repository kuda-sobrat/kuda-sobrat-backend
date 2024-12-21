<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ContextEvent. Предназначено для предварительной обработки мероприятия
 *
 * @property $id
 * @property $community_id
 * @property $event_id
 * @property $context_id
 * @property $status
 * @property $name
 * @property $description
 * @property $start_datetime
 * @property $end_datetime
 * @property $location
 * @property ContextPost $contextPost
 */
class ContextEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'unique_hash',
        'community_id',
        'event_id',
        'context_id',
        'status',
        'name',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    /**
     * Связь с communities
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Связь с events
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Связь с context_responses
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contextResponses()
    {
        return $this->hasMany(ContextResponse::class, 'id', 'context_id');
    }

    /**
     * Связь с context_posts
     *
     * @return mixed
     */
    public function contextPost()
    {
        return $this->contextResponses[0]->contextPost();
    }
}
