<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Ответ по контексту
 *
 * @property $context_id
 * @property $response
 * @property $model
 * @property ContextRequest $contextRequest
 * @property ContextPost $contextPost
 */
class ContextResponse extends Model
{
    use HasFactory;

    protected $fillable = ['context_id', 'response', 'model'];


    public function contextRequest()
    {
        return $this->belongsTo(ContextRequest::class, 'context_id');
    }

    public function contextPost()
    {
        return $this->contextRequest->contextPost();
    }
}
