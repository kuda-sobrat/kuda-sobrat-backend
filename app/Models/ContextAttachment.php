<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


// TODO: определить поля, миграция для типов вложений
/**
 * Вложение к записи
 *
 * @property integer $id
 * @property integer $context_id
 * @property string $type
 * @property string $url
 * @property string $title
 * @property string $text
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 */
class ContextAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'context_id',
        'type',
        'url',
        'title',
        'text',
        'created_at',
        'updated_at',
    ];

    public function contextPost()
    {
        return $this->belongsTo(ContextPost::class, 'context_id');
    }
}
