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
 * @property $text
 * @property $processed_text
 * @property $unique_hash
 * @property $context_request_id
 * @property $context_response_id
 * @property $tags
 * @property $created_at
 * @property $updated_at
 * @property Collection<ContextAttachment> $attachments
 * @property CommunitySocialLink socialLink
 */
class ContextPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_link_id',
        'event_id',
        'source_id',
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

    /**
     * Связь с таблицей community_social_links.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function socialLink()
    {
        return $this->hasOne(CommunitySocialLink::class, 'id', 'social_link_id');
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
