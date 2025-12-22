<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Driver extends Authenticatable
{
    use HasApiTokens;
    protected $fillable = [
        'primary_phone',
        'rand_number',
        'driver_temp_token',
        'phone_confirm_sent',
        'status',
        'first_name',
        'middle_name',
        'last_name',
        'ssn_sin',
        'date_of_birth',
    ];

    protected $casts = [
        'status' => 'boolean',
        'date_of_birth' => 'date',
        'phone_confirm_sent' => 'datetime',
    ];
}
