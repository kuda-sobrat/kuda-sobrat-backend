<?php

namespace App\Models;

use App\Enums\ProcessStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Ответ по контексту
 *
 * @property $id
 * @property $context_id
 * @property $response
 * @property $jsonResponse
 * @property $model
 * @property ContextRequest $contextRequest
 * @property ContextPost $contextPost
 */
class ContextResponse extends Model
{
    use HasFactory;

    protected $fillable = ['context_id', 'status', 'response', 'model'];

    protected $casts = [
        'status' => ProcessStatusEnum::class
    ];

    public function contextRequest()
    {
        return $this->belongsTo(ContextRequest::class, 'context_id');
    }

    public function contextPost()
    {
        return $this->contextRequest->contextPost();
    }

    /**
     * Сериализация ответа в json
     *
     * @return Object|null
     */
    public function getJsonResponseAttribute(): Object|null
    {

        $json = json_decode($this->attributes['response']);
        if ($this->response != null && empty($tmp)) {
            $pattern = '/```json\s*(\{.*?\})\s*```/s';
            if (preg_match($pattern, $this->response, $matches)) {
                $json = json_decode($matches[1]);
                // Теперь $jsonString содержит ваш JSON
            }
        }
        return $json;
    }
}
