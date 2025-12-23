<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class GeneralInformation extends Model
{
    protected $fillable = [
        'license_denial',
        'has_driving_convictions',
        'has_substance_conviction',
        'positive_substance_violation',
        'has_moving_violation_or_accident_last_3_years',
        'has_violations_accidents',
        'eligible_for_us_employment',
        'speak_english',
    ];

    protected $casts = [
        'license_denial' => 'boolean',
        'has_driving_convictions' => 'boolean',
        'has_substance_conviction' => 'boolean',
        'positive_substance_violation' => 'boolean',
        'has_moving_violation_or_accident_last_3_years' => 'boolean',
        'has_violations_accidents' => 'boolean',
        'eligible_for_us_employment' => 'boolean',
        'speak_english' => 'boolean',
    ];
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
