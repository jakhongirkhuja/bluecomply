<?php

namespace App\Models\Company;

use App\Models\Driver\Vehicle;
use App\Models\General\VehicleDocumentType;
use Illuminate\Database\Eloquent\Model;

class VehicleDocumentFile extends Model
{
    protected $fillable = [
        'vehicle_id',
        'vehicle_document_type_id',
        'company_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function type()
    {
        return $this->belongsTo(
            VehicleDocumentType::class,
            'vehicle_document_type_id'
        );
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
