<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

/**
 *  @property Collection<Interest> $interests
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /** @var string[] $fillable */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /** @var string[] $hidden */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @var string[] $casts */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Отношение к интересам
    public function interests()
    {
        return $this->belongsToMany(Interest::class);
    }
}
