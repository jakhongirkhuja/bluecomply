<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class EmploymentVerificationResponse extends Model
{
    protected $fillable = [
        'employment_verification_id',

        'position_held',
        'driver_class',
        'driver_type',
        'eligible_for_rehire',
        'was_terminated',
        'termination_reason',
        'fmcsr_subject',
        'safety_sensitive_job',
        'area_driven',
        'equipment_driven',
        'trailer_driven',
        'loads_hailed',

        'alcohol_0_04_or_higher',
        'verified_positive_drug_test',
        'refused_test',
        'other_dot_violation',
        'reported_previous_violation',
        'return_to_duty_completed',
        'drug_alcohol_comments',
    ];

    public function verification()
    {
        return $this->belongsTo(EmploymentVerification::class);
    }

    public function accidents()
    {
        return $this->hasMany(EmploymentVerificationAccident::class, 'response_id');
    }
}
