<?php

namespace App\Models;

use App\Contracts\Interfaces\GeocodingServiceInterface;
use App\Enums\ProcessStatusEnum;
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
 * @property $location
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
        'community_id',
        'status',
        'name',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
        'unique_hash',
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
    ];

    public static function booted()
    {
        static::saving(function ($event) {
            if ($event->isDirty('location')) {
                // Получаем реализацию геокодера через контейнер
                $geocodingService = app(GeocodingServiceInterface::class);

                $coordinates = $geocodingService->geocode($event->location);

                if ($coordinates) {
                    $event->latitude = $coordinates['latitude'];
                    $event->longitude = $coordinates['longitude'];
                    $event->formatted_address = $coordinates['formatted_address'];
                } else {
                    // Обработка случая, когда геокодирование не удалось
                    $event->latitude = null;
                    $event->longitude = null;
                    $event->formatted_address = null;
                }
            }
        });

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
        // TODO: location
        $unique_hash = Event::generateUniqueHash($this->community_id, $this->start_datetime, $this->location . $this->end_datetime);
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

    /**
     * Участники мероприятия.
     */
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_attendees')
            ->withTimestamps();
    }
}
