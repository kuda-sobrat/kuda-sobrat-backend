<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Запрос с контекстом
 */
class ContextRequest extends Model
{
    use HasFactory;

    public function responses()
    {
        return $this->hasMany(ContextResponse::class, 'context_request_id');
    }
}
