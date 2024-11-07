<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Вложение к записи
 */
class ContextAttachment extends Model
{
    use HasFactory;

    public function contextPost()
    {
        return $this->belongsTo(ContextPost::class);
    }
}
