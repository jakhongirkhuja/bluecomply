<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class DriverAddress extends Model
{
    protected $fillable = [
        'driver_id',
        'address',
        'move_in',
        'move_out',
        'city_id',
        'state_id',
        'zip',
        'currently_live',
    ];
    protected $casts = [
        'move_in'        => 'datetime',
        'move_out'       => 'datetime',
        'currently_live'=> 'boolean',
    ];
}
