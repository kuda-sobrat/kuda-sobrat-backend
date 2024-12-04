<?php

namespace App\Models;

use App\Enums\ProcessStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
 * @property $location
 * @property $unique_hash
 * @property $created_at
 * @property $updated_at
 * @property Collection<Interest> $interests
 * @property Collection<ContextPost> $contextPosts
 */
class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_id',
        'status',
        'name',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
        'unique_hash',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'status' => ProcessStatusEnum::class,
    ];

    public static function booted()
    {
        static::creating(function ($event) {
            $event->setupUniqueHash();
        });

        static::updating(function ($event) {
            $event->setupUniqueHash();
        });
    }

    public function contextPosts()
    {
        return $this->hasMany(ContextPost::class)->with('community');
    }

    public function attachments() {
        return $this->hasManyThrough(
            ContextAttachment::class,
            ContextPost::class,
            'event_id',
            'context_id',
            'id',
            'id'
        );
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'event_interest');
    }

    public function setupUniqueHash()
    {
        $this->unique_hash = Event::generateUniqueHash($this->community_id, $this->start_datetime, $this->location);
    }

    public static function generateUniqueHash(int $communityId, \DateTime $startDatetime, string $location): string
    {
        return md5("$communityId|{$startDatetime->getTimestamp()}|$location");
    }

    public function findOrSave()
    {
        $uniqueHash = Event::generateUniqueHash($this->community_id, $this->start_datetime, $this->location);

        /** @var Event $event */
        $event = Event::where('unique_hash', $uniqueHash)->first();

        if (!$event) {
            $this->save();
        } else {
            $this->id = $event->id;
            $this->exists = true;
        }

        return $this;
    }
}
