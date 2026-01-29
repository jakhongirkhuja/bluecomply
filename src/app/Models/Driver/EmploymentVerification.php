<?php

namespace App\Models\Driver;

use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Model;

class EmploymentVerification extends Model
{
    protected $fillable = [
        'driver_id',
        'company_id',
        'direction',
        'employer_name',
        'employer_usdot',
        'contact_email',
        'contact_phone',
        'fax_number',
        'created_by_company',
        'employment_start_date',
        'employment_end_date',
        'method',
        'status',
        'sent_at',
        'completed_at',
        'notes',
        'created_by',
        'description',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function companyby()
    {
        return $this->belongsTo(Company::class, 'created_by_company');
    }
    public function events()
    {
        return $this->hasMany(EmploymentVerificationEvent::class);
    }

    public function responses()
    {
        return $this->hasMany(EmploymentVerificationResponse::class);
    }

}
