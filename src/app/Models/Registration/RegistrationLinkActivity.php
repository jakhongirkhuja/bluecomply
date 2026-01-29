<?php

namespace App\Models\Registration;

use Illuminate\Database\Eloquent\Model;

class RegistrationLinkActivity extends Model
{
    protected $fillable = [
        'registration_link_id',
        'viewed_at',
        'started_at',
        'user_agent',
        'ip_address',
        'view_count'
    ];

    public function registrationLink()
    {
        return $this->belongsTo(RegistrationLink::class);
    }
}
