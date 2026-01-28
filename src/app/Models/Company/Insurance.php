<?php

namespace App\Models\Company;

use App\Models\General\VehicleInsuranceType;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    protected $fillable = ['company_id','vehicle_insurance_type_id','company_type_id','related_to','expires_at'];
    public function files(){
        return $this->hasmany(InsuranceFile::class);
    }
    public function insuranceType(){
        return $this->belongsTo(VehicleInsuranceType::class,'vehicle_insurance_type_id');
    }
}
