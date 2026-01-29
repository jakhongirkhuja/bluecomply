<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class RestrictionType extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];
}
