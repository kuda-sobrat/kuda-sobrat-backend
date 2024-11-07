<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Пост сформированный chatGPT
 */
class ContextPost extends Model
{
    use HasFactory;

    protected $fillable = ['tags'];

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
        return $this->hasMany(ContextAttachment::class);
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
