<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class EmployerInformation extends Model
{
    protected $fillable = [
        'driver_id',
        'type_engagement',
        'name',
        'position',
        'address',
        'start_date',
        'end_date',
        'current_employer',
        'reason_for_leaving',
        'company_contact_name',
        'company_contact_phone',
        'company_contact_email',
        'company_contact_allow',
        'safety_regulations',
        'sensitive_functions',
        'motor_vehicle',
        'type',
        'equipment_operated',
    ];

    protected $casts = [
        'current_employer' => 'boolean',
        'company_contact_allow' => 'boolean',
        'safety_regulations' => 'boolean',
        'sensitive_functions' => 'boolean',
        'motor_vehicle' => 'boolean',
        'equipment_operated' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
