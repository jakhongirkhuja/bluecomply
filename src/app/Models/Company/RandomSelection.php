<?php

namespace App\Models\Company;

use App\Models\Driver\Driver;
use Illuminate\Database\Eloquent\Model;

class RandomSelection extends Model
{
    protected $fillable = [
        'driver_id',
        'company_id',
        'service',
        'is_dot',
        'selected_at',
        'random_pool_membership_id',
        'drug_test_order_id',
        'status',
    ];
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function poolMembership()
    {
        return $this->belongsTo(RandomPoolMembership::class, 'random_pool_membership_id');
    }
    public function order()
    {
        return $this->belongsTo(DrugTestOrder::class, 'drug_test_order_id');
    }
}
