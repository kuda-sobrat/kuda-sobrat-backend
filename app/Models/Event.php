<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Мероприятие
 *
 * @property $id
 * @property $community_id
 * @property $name
 * @property $description
 * @property $start_datetime
 * @property $end_datetime
 * @property $location
 * @property $unique_hash
 */
class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_id',
        'name',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
        'unique_hash',
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
        return $this->hasMany(ContextPost::class);
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
