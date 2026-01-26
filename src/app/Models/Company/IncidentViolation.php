<?php

namespace App\Models\Company;

use App\Models\Driver\Driver;
use Illuminate\Database\Eloquent\Model;

class IncidentViolation extends Model
{
    protected $fillable = [
        'company_id',
        'incident_id',
        'driver_id',
        'code',
        'unit',
        'description',
        'violation_category_id',
        'violation_oos',
        'violation_corrected',
    ];

    protected $casts = [
        'violation_oos' => 'boolean',
        'violation_corrected' => 'boolean',
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function category()
    {
        return $this->belongsTo(ViolationCategory::class, 'violation_category_id');
    }
}
