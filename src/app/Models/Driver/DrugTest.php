<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class DrugTest extends Model
{
    protected $fillable = [
        'driver_id',
        'test_type',
        'random_pool_membership',
        'reason',
        'requested_date',
        'collected_date',
        'completed_date',
        'result',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
