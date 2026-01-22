<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class DriverTag extends Model
{
    protected $fillable = [
        'company_id',
        'driver_id',
        'tag',
        'user_id'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
