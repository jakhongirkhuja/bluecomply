<?php

namespace App\Models\Admin;

use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'driver_limit',
        'features'
    ];
    protected $casts = [
        'features' => 'array',
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
