<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Просмотр мероприятия
 *
 * @property $id - id записи
 * @property $event_id - ID мероприятия
 * @property $user_id - ID пользователя
 * @property $session_id - Session ID
 * @property $ip_address - IP адрес
 * @property $user_agent - User Agent
 * @property $viewed_at - Дата просмотра
 */
class EventView extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'event_id',
        'user_id',
        'ip_address',
        'user_agent',
        'viewed_at',
    ];
}
