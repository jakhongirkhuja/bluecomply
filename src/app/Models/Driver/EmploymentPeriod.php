<?php

namespace App\Models\Driver;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EmploymentPeriod extends Model
{
    protected $fillable = [
        'driver_id',
        'company_id',
        'start_date',
        'end_date',
        'status',
        'termination_reason',
        'rehired',
        'notes',
        'notify_driver',
        'payed_date',
        'created_by',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }
}
