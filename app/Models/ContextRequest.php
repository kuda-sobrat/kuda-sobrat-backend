<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Запрос с контекстом
 *
 * @property $id
 * @property $type
 * @property $context
 * @property $status
 * @property $created_at
 * @property $updated_at
 */
class ContextRequest extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'context', 'context_id', 'status'];

    public function responses()
    {
        return $this->hasMany(ContextResponse::class, 'context_request_id');
    }
}
