<?php

namespace App\Models\Company;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'channels',
    ];
    protected $casts = [
        'channels' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
