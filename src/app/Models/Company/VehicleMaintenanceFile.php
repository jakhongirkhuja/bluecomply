<?php

namespace App\Models\Company;

use App\Models\Driver\Vehicle;
use App\Models\General\VehicleMaintenanceType;
use Illuminate\Database\Eloquent\Model;

class VehicleMaintenanceFile extends Model
{
    protected $fillable = [
        'vehicle_id',
        'vehicle_maintenance_type_id',
        'vehicle_maintenance_id',
        'company_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
    ];

    public function maintenance()
    {
        return $this->belongsTo(VehicleMaintenance::class, 'vehicle_maintenance_id');
    }

    public function type()
    {
        return $this->belongsTo(VehicleMaintenanceType::class, 'vehicle_maintenance_type_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
