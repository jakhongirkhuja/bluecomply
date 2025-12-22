<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'company_name',
        'status',
        'user_id',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
