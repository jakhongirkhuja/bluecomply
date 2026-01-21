<?php

namespace App\Models\Driver;

use App\Models\Company\VehicleDocument;
use App\Models\Company\VehicleInsurance;
use App\Models\Company\VehicleMaintenance;
use App\Models\General\VehicleType;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'company_id',
        'type_id',
        'unit_number',
        'status',
        'make',
        'model',
        'year',
        'vin',
        'plate',
        'state_id',
        'expire_at',
        'inspection_at',
    ];
//    protected $dates = [
//        'type_id'=>'digits',
//    ];
    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'driver_vehicles')->where('drivers.company_id', $this->company_id);
    }
    public function type(){
        return $this->belongsTo(VehicleType::class);
    }
    public function documents()
    {
        return $this->hasMany(VehicleDocument::class);
    }
    public function registration(){
        return $this->hasOne(VehicleDocument::class)->where('vehicle_document_type_id',1)
            ->where('current', true);
    }
    public function inspection(){
        return $this->hasOne(VehicleDocument::class)->where('vehicle_document_type_id',4)
            ->where('current', true);
    }
    public function insurance(){
        return $this->hasOne(VehicleInsurance::class)->where('vehicle_insurance_type_id',1)->where('current', true);
    }
    public function registrations(){
        return $this->hasMany(VehicleDocument::class);
    }
}
