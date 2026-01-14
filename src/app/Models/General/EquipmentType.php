<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class EquipmentType extends Model
{
    protected $fillable = [
        'code','name','description','requires_endorsement','category'
    ];
}
