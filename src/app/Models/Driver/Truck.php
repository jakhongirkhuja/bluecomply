<?php

namespace App\Models\Driver;

use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    protected $fillable = [
        'driver_id',
        'name',
        'number',
        'company_id',
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
