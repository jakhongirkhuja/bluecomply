<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'level',
        'state',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

}
