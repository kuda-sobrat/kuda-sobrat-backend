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

    protected $fillable = ['context_id', 'response', 'model'];

    public function request()
    {
        return $this->belongsTo(ContextRequest::class, 'context_request_id');
    }
}
