<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class Endorsement extends Model
{
    protected $fillable = [
        'company_id',
        'endorsements',
        'twic_card_path',
        'driver_id',
    ];
    protected $casts = [
        'endorsements' => 'array',
    ];
}
