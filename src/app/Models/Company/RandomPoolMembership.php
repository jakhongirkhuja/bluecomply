<?php

namespace App\Models\Company;

use App\Models\Driver\Driver;
use Illuminate\Database\Eloquent\Model;

class RandomPoolMembership extends Model
{
    protected $fillable = [
        'driver_id',
        'company_id',
        'year',
        'date',
        'service',
        'is_dot',
        'start_date',
        'end_date',
        'note',
        'pool_type',       // drug, alcohol, both
        'status',          // active, inactive
        'last_random_test_date',
        'selected_at',
    ];
    protected $casts = [
        'is_dot' => 'boolean',
        'date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function testResults()
    {
        return $this->hasMany(DrugTestResult::class, 'driver_id', 'driver_id');
    }
    public function isActive()
    {
        return $this->status === 'active' && (is_null($this->end_date) || $this->end_date >= now());
    }

    public function markSelected()
    {
        $this->update(['selected_at' => now()]);
    }
    public function dueForRandomTest()
    {
        return $this->isActive() && (!$this->last_random_test_date || $this->last_random_test_date->lt(now()->subYear()));
    }
}
