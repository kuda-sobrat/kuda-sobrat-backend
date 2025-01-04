<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id - ID вложения
 * @property $event_id - ID мероприятия
 * @property $type - Тип вложения
 * @property $url - Ссылка
 * @property $title - Название
 * @property $text - Текст
 */
class EventAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
      'event_id',
      'type',
      'url',
      'title',
      'text',
    ];

    public function event() {
        return $this->belongsTo(Event::class);
    }
}
