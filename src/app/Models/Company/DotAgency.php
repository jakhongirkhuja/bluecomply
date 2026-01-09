<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class DotAgency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'status',
    ];
}
