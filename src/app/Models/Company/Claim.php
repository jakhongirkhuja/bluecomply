<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $fillable = [
        'incident_id',
        'driver_id',
        'vehicle_id',
        'trailer_id',
        'claim_types',
        'claim_number',
        'carrier_number',
        'adjuster_name',
        'adjuster_contact',
        'status',
        'deductible_amount',
        'insurance_paid',
        'opposing_party_name',
        'opposing_party_insurance',
        'description',
    ];

    // Cast claim_types as array
    protected $casts = [
        'claim_types' => 'array',
    ];

    // Relationships
    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function documents()
    {
        return $this->hasMany(ClaimDocument::class);
    }
}
