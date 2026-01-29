<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class Restriction extends Model
{
    protected $fillable = [
        'restriction_type_id',
        'driver_id',
        'company_id',
    ];
}
