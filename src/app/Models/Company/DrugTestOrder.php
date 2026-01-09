<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class DrugTestOrder extends Model
{
    protected $fillable = [
        'driver_id',
        'company_id',
        'reference_id',
        'i3_case_number',
        'test_type',
        'reason',
        'dot_agency',
        'observed',
        'expiration_date',
        'package_code',
        'status',
        'notes',
    ];
}
