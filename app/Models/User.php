<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'password_set',
        'email_verified_at',
        'last_login_at',
        'otp_hash',
        'otp_created_at',
        'otp_expires_at',
        'social_provider',
        'social_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_hash',           // optional: hide OTP hash from JSON responses
        'otp_created_at',
        'otp_expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'last_login_at'     => 'datetime',
            'otp_created_at'    => 'datetime',
            'otp_expires_at'    => 'datetime',
            'password_set'      => 'boolean',
    ];

    public function forums()
    {
        return $this->hasMany(Forum::class, 'created_by');
    }

    public function channels()
    {
        return $this->hasMany(Channel::class, 'created_by');
    }

    public function threads()
    {
        return $this->hasMany(Thread::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    public function channelMemberships()
    {
        return $this->belongsToMany(Channel::class, 'channel_user')->withPivot('role')->withTimestamps();
    }
}
