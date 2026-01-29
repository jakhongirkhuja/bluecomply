<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Company\Role;
use App\Models\Company\UserApiSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role_id',
        'password',
        'phone',
        'last_login',
        'status',
        'rand_number',
        'phone_confirm_at',
        'phone_confirm_sent',
        'address',
        'city',
        'state_id',
        'zip_code',
        'sms_2fa_enabled',
        'totp_enabled',
        'appearance',
        'time_zone',
        'language',
        'date_format',
        'time_format',
        'signature',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',

    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'sms_2fa_enabled'=>'boolean',
            'totp_enabled'=>'boolean',
            'phone_confirm_sent' => 'datetime',
            'time_format' => 'integer',
        ];
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }
    public function apiSessions()
    {
        return $this->hasMany(UserApiSession::class);
    }
}
