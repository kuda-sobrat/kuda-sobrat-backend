<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Ответ по контексту
 */
class ContextResponse extends Model
{
    use HasFactory;

    public function request()
    {
        return $this->belongsTo(ContextRequest::class, 'context_request_id');
    }
}
