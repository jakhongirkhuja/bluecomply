<?php

namespace App\Models\Company;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserApiSession extends Model
{
    protected $fillable = [
        'user_id',
        'device',
        'location',
        'token_id',
        'login_at',
        'last_active_at',
    ];

    protected $dates = ['login_at', 'last_active_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
