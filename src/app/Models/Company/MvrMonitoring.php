<?php

namespace App\Models\Company;

use App\Models\Driver\Driver;
use Illuminate\Database\Eloquent\Model;

class MvrMonitoring extends Model
{

    protected $fillable = [
        'driver_id',
        'company_id',
        'enrolled',
        'monthly_cost',
    ];

    protected $casts = [
        'enrolled' => 'boolean',
        'monthly_cost' => 'decimal:2',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
