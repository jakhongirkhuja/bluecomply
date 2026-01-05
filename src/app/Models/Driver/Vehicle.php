<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'type',
        'unit_number',
        'make',
        'vin',
        'plate',
        'plate_state',
    ];
}
