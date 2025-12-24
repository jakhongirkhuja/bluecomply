<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class PersonalInformation extends Model
{
    protected $fillable = [
        'driver_id',
        'first_name',
        'middle_name',
        'last_name',
        'ssn_sin',
        'date_of_birth',
        'email',
        'email_confirm_token',
        'email_confirmed',
    ];

    protected $casts = [
        'email_confirmed' => 'boolean',
        'date_of_birth' => 'date',
    ];
}
