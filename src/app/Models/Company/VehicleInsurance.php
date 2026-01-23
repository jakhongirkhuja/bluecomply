<?php

namespace App\Models\Company;

use App\Models\Driver\Vehicle;
use App\Models\General\VehicleInsuranceType;
use Illuminate\Database\Eloquent\Model;

class VehicleInsurance extends Model
{
    protected $fillable = [
        'vehicle_id',
        'vehicle_insurance_type_id',
        'company_id',
        'description',
        'expires_at',
        'current',
    ];


    protected $casts = [
        'expires_at' => 'datetime',
        'current' => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function type()
    {
        return $this->belongsTo(VehicleInsuranceType::class, 'vehicle_insurance_type_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function files()
    {
        return $this->hasMany(VehicleInsuranceFile::class);
    }

}
