<?php

namespace App\Models\Company;

use App\Models\Driver\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'action',
        'details',
        'user_id',
        'driver_id',
        'user_type',
        'action_at',
        'action_table_name',
        'action_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function driver(){
        return $this->belongsTo(Driver::class);
    }


    public function subject()
    {
        return $this->morphTo();
    }
}
