<?php

namespace App\Models\Driver;

use App\Models\General\States;
use Illuminate\Database\Eloquent\Model;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class DriverAddress extends Model
{
    protected $fillable = [
        'driver_id',
        'address',
        'move_in',
        'move_out',
        'city_id',
        'state_id',
        'zip',
        'house',
        'country_id',
        'currently_live',
    ];
    protected $casts = [
        'move_in'        => 'datetime',
        'move_out'       => 'datetime',
        'currently_live'=> 'boolean',
    ];
    public function states(){
        return $this->belongsTo(States::class,'state_id','id');
    }
}
