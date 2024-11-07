<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Пост сформированный chatGPT
 *
 * @property $id
 * @property $event_id
 * @property $source_id
 * @property $source_type
 * @property $community_id
 * @property $text
 * @property $processed_text
 * @property $unique_hash
 * @property $context_request_id
 * @property $context_response_id
 * @property $tags
 * @property $created_at
 * @property $updated_at
 * @property Collection<ContextAttachment> $attachments
 */
class ContextPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'source_id',
        'source_type',
        'community_id',
        'text',
        'processed_text',
        'unique_hash',
        'context_request_id',
        'context_response_id',
        'tags',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'post_interest');
    }

    public function attachments()
    {
        return $this->hasMany(ContextAttachment::class, 'context_id');
    }

    public function contextRequest()
    {
        return $this->belongsTo(ContextRequest::class, 'context_request_id');
    }

    public function contextResponse()
    {
        return $this->belongsTo(ContextResponse::class, 'context_response_id');
    }
}
