<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'driver_id',
        'submitted',
        'application_data',
        'confirmation_number',
        'used_at',
        'used_ip',
    ];

    protected $casts = [
        'application_data' => 'array',
        'submitted' => 'boolean',
        'used_at' => 'datetime',
    ];

    public function personalInformation()
    {
        return $this->hasOne(PersonalInformation::class);
    }

    public function address()
    {
        return $this->hasOne(DriverAddress::class);
    }
}
