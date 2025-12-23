<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class DriverSign extends Model
{
    protected $fillable = [
        'driver_id',
        'sign_path'
    ];
    public function getFullPathAttribute()
    {
        if (!$this->sign_path) return null;

        return asset('storage/' . $this->sign_path);
    }
}
