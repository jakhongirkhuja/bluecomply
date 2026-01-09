<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class Rejection extends Model
{
    protected $fillable = [
        'driver_id',
        'company_id',
        'rejection_reason_id',
        'description'
    ];
    public function company(){
        return $this->belongsTo(Company::class);
    }
}
