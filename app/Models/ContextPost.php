<?php

namespace App\Models;

use App\Enums\ProcessStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Пост сформированный chatGPT
 *
 * @property $id
 * @property $social_link_id
 * @property $event_id
 * @property $status
 * @property $source_id
 * @property $text
 * @property $processed_text
 * @property $unique_hash
 * @property $tags
 * @property $created_at
 * @property $updated_at
 * @property Event|null $event
 * @property Collection<ContextAttachment> $attachments
 * @property CommunitySocialLink socialLink
 */
class ContextPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_link_id',
        'event_id',
        'status',
        'source_id',
        'text',
        'processed_text',
        'unique_hash',
        'tags',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => ProcessStatusEnum::class,
        'tags' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * TODO: Определить связь
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'post_interest');
    }

    /**
     * Приложения к записи
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
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
