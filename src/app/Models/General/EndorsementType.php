<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class EndorsementType extends Model
{
    protected $fillable = [
        'code','name','description'
    ];
}
