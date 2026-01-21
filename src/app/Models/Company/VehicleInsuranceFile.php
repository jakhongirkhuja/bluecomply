<?php

namespace App\Models\Company;

use App\Models\Driver\Vehicle;
use Illuminate\Database\Eloquent\Model;

class VehicleInsuranceFile extends Model
{
    protected $fillable = [
        'vehicle_id',
        'vehicle_insurance_id',
        'vehicle_insurance_type_id',
        'company_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
    ];

    public function insurance()
    {
        return $this->belongsTo(VehicleInsurance::class, 'vehicle_insurance_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $bytes > 1024; $i++) $bytes /= 1024;
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
