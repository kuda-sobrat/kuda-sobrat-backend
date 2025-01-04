<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string | null $name
 * @property string | null $description
 * @property Collection<Event> $events
 * @property Collection<ContextEvent> $contextEvents
 */
class EventGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Связь с моделью Event.
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'event_group_id');
    }

    /**
     * Связь с моделью Event.
     */
    public function contextEvents()
    {
        return $this->hasMany(ContextEvent::class, 'event_group_id');
    }
}
