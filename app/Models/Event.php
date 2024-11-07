<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    public function contextPosts()
    {
        return $this->hasMany(ContextPost::class);
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'event_interest');
    }
}
