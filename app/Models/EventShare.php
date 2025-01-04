<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Публикация записи (шаринг)
 *
 * @property int $event_id - ID мероприятия
 * @property int $user_id - ID пользователя
 * @property int|null $social_network_id - ID социальной сети
 * @property \DateTime $shared_at
 */
class EventShare extends Model
{
    use HasFactory;

    protected $fillable = [
      'event_id',
      'user_id',
      'social_network_id',
      'shared_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'shared_at' => 'datetime'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
