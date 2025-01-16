<?php

namespace App\Models;

use App\Casts\PointCast;
use App\Enums\ProcessStatusEnum;
use App\Support\Point;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Мероприятие
 *
 * @property $id
 * @property $community_id
 * @property $status
 * @property $name
 * @property $description
 * @property $start_datetime
 * @property $end_datetime
 * @property Point $location
 * @property string $location_name
 * @property $unique_hash
 * @property $created_at
 * @property $updated_at
 * @property $event_group_id
 * @property Collection<Interest> $interests
 * @property Collection<ContextPost> $contextPosts
 * @property Collection<Community> $communities
 * @property Collection<EventSource> $eventSources
 * @property EventGroup $eventGroup
 */
class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'status',
        'name',
        'description',
        'start_datetime',
        'end_datetime',
        'latitude',
        'longitude',
        'location',
        'location_name',
        'unique_hash',
        'popularity_score',
        'attendees',
        'shares',
        'views',
        'is_archived',
        'archived_at',
        'marked_for_deletion_at',
        'deleted_at',
        'event_group_id',
    ];

    protected $with = [
        'attachments',
        'eventGroup',
        'communities',
        'eventSources',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'status' => ProcessStatusEnum::class,
        'archived_at' => 'datetime',
        'marked_for_deletion_at' => 'datetime',
        'deleted_at' => 'datetime',
        'location' => PointCast::class,
    ];

    public function contextPosts()
    {
        return $this->hasMany(ContextPost::class)->with('community');
    }

    public function attachments() {
        return $this->hasMany(EventAttachment::class);
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'event_interest');
    }

    /**
     * Участники мероприятия.
     */
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_attendees')
            ->withTimestamps();
    }

    /**
     * Связь с моделью EventGroup.
     */
    public function eventGroup()
    {
        return $this->belongsTo(EventGroup::class, 'event_group_id');
    }

    /**
     * Связанные сообщества
     *
     * @return BelongsToMany
     */
    public function communities()
    {
        return $this->belongsToMany(Community::class);
    }

    /**
     * Связанные источники
     *
     * @return HasMany
     */
    public function eventSources(): HasMany
    {
        return $this->hasMany(EventSource::class);
    }
}
