<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class EmploymentVerificationAccident extends Model
{
    protected $fillable = [
        'response_id',
        'accident_date',
        'dot_recordable',
        'preventable',
        'city',
        'state',
        'injuries',
        'fatalities',
        'hazardous_material_involved',
        'equipment_driven',
        'description',
        'comments',
    ];

    public function response()
    {
        return $this->belongsTo(EmploymentVerificationResponse::class, 'response_id');
    }
}
