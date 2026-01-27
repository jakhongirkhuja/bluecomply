<?php

namespace App\Models\Company;

use App\Models\Driver\Driver;
use App\Models\General\ChallengeCategory;
use App\Models\General\ChallengeType;
use Illuminate\Database\Eloquent\Model;

class DataqChallenge extends Model
{
    protected $fillable = [
        'company_id',
        'request_id',
        'status',
        'incident_id',
        'inspection_id',
        'driver_id',
        'truck_id',
        'report_number',
        'state_id',
        'manual_equipment_unit',
        'type_id',
        'category_id',
        'explanation',
        'internal_notes',
    ];

    // Relationships
    public function type()
    {
        return $this->belongsTo(ChallengeType::class, 'type_id');
    }

    public function category()
    {
        return $this->belongsTo(ChallengeCategory::class, 'category_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function inspection(){
        return $this->belongsTo(Incident::class,'inspection_id')->where('type','inspections');
    }
    public function incident(){
        return $this->belongsTo(Incident::class);
    }
    public function files()
    {
        return $this->hasMany(DataqChallengeDocument::class, 'dataq_challenge_id');
    }
}
