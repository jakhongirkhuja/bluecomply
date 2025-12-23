<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class LicenseDetail extends Model
{
    protected $fillable = [
        'driver_id',
        'license_number',
        'city_id',
        'state_id',
        'license_issue_date',
        'license_expiration',
        'driver_license_front_path',
        'driver_license_back_path',
        'current',
    ];
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function getFrontFullPathAttribute()
    {
        return $this->driver_license_front_path
            ? asset('storage/' . $this->driver_license_front_path)
            : null;
    }

    public function getBackFullPathAttribute()
    {
        return $this->driver_license_back_path
            ? asset('storage/' . $this->driver_license_back_path)
            : null;
    }
}
