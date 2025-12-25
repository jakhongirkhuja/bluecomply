<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class Termination extends Model
{
    protected $fillable = [
        'driver_id',
        'termination_date',
        'termination_reason',
        'payed_date',
        'rehire',
        'notes',
        'notify_driver',
    ];

    protected $casts = [
        'termination_date' => 'date',
        'payed_date' => 'date',
        'notify_driver' => 'boolean',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
