<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class LinkVerification extends Model
{
    protected $fillable =[
        'link_id',
        'driver_id'
    ];
}
