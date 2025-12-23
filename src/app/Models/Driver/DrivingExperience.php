<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class DrivingExperience extends Model
{
    protected $fillable = [
        'driver_id',
        'years_of_experience',
        'miles_driven',
        'from',
        'to',
        'equipment_operated',
        'state_id',
    ];

    protected $casts = [
        'equipment_operated' => 'array',
        'from'               => 'date',
        'to'                 => 'date',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
