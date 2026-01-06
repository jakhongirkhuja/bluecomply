<?php

namespace App\Models\Company;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use LogsActivity;
    protected $fillable = [
        'incident_id',
        'driver_id',
        'company_id',
        'type',
        'other_type',
        'claim_number',
        'carrier_name',
        'adjuster_name',
        'adjuster_contact',
        'status',
        'deductible_amount',
        'insurance_paid',
        'opposing_party_name',
        'opposing_party_insurance',
        'repair_vendor_name',
        'shipper_name',
        'damage_type',
        'cargo_value',
        'cargo_loss_amount',
        'internal_claim_number',
        'opposing_carrier_name',

        'description',
    ];

    protected $casts = [
        'claim_types' => 'array',
    ];
    protected $hidden = [
        'identifier'
    ];
    protected $appends = [
        'identifier-formatted',
    ];
    public function getIdentifierFormattedAttribute(): string
    {
        return 'CL-' . $this->identifier;
    }
    protected static function boot()
    {
        parent::boot();

        // Перед созданием модели
        static::creating(function ($claim) {
            if (!$claim->identifier) {
                $claim->identifier = self::generateIdentifier();
            }
        });
    }

    protected static function generateIdentifier(): int
    {
        $last = self::latest('identifier')->first();

        return $last && $last->identifier? $last->identifier + 1 : 3958;
    }
    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function files()
    {
        return $this->hasMany(ClaimDocument::class);
    }
}
