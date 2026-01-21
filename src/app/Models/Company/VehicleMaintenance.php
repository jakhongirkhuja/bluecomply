<?php

namespace App\Models\Company;

use App\Models\Driver\Vehicle;
use App\Models\General\VehicleMaintenanceType;
use Illuminate\Database\Eloquent\Model;

class VehicleMaintenance extends Model
{
    protected $fillable = [
        'vehicle_id',
        'vehicle_maintenance_type_id',
        'service_date',
        'mileage',
        'vendor_name',
        'description',
        'next_due_type',
        'next_due_date',
        'company_id',
    ];
    protected $casts = [
        'service_date' => 'date',
        'next_due_date' => 'date',
        'mileage' => 'integer',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function maintenanceType()
    {
        return $this->belongsTo(VehicleMaintenanceType::class, 'vehicle_maintenance_type_id');
    }

    public function files()
    {
        return $this->hasMany(VehicleMaintenanceFile::class, 'vehicle_maintenance_id');
    }
}
