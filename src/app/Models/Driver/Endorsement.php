<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class Endorsement extends Model
{
    protected $fillable = [
        'endorsements',
        'twic_card_path',
    ];
    protected $casts = [
        'endorsements' => 'array',
    ];
}
