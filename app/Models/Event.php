<?php

namespace App\Models;

use App\Casts\PointCast;
use App\Enums\ProcessStatusEnum;
use App\Support\Point;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
 * @property Collection<Interest> $interests
 * @property Collection<ContextPost> $contextPosts
 */
class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
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
}
