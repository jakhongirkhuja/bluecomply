<?php

namespace App\Models\Company;

use App\Models\Driver\Vehicle;
use App\Models\General\VehicleDocumentType;
use Illuminate\Database\Eloquent\Model;

class VehicleDocument extends Model
{
    protected $fillable = [
        'vehicle_id',
        'vehicle_document_type_id',
        'company_id',
        'description',
        'status',
        'current',
        'expires_at',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Document type relation
     */
    public function type()
    {
        return $this->belongsTo(
            VehicleDocumentType::class,
            'vehicle_document_type_id'
        );
    }

    /**
     * Company relation
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Optional: Document files relation (if you add this relation)
     */
    public function files()
    {
        return $this->hasMany(VehicleDocumentFile::class);
    }

    /**
     * Optional: Scope to only active documents
     */
//    public function scopeActive($query)
//    {
//        return $query->where('status', 'active');
//    }
}
