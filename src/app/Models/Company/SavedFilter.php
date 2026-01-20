<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class SavedFilter extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'filters'
    ];

    protected $casts = [
        'filters' => 'array'
    ];
}
