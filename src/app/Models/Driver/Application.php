<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'registration_link_id',
        'driver_id',
        'step',
        'submitted',
        'application_data',
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
