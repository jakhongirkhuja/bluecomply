<?php

namespace App\Models\Company;

use App\Models\Admin\Plan;
use App\Models\Driver\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'company_name',
        'status',
        'user_id',
        'tenet_id',
        'dot_number',
        'all_drivers',
        'name',
        'status',
        'subscription_plan_id',
        'plan_start_date',
        'primary_name',
        'primary_email',
        'primary_phone',
        'der_name',
        'der_email',
        'der_phone',
        'notes',
        'features_overrides',
        'custom_forms'.
        'drivers',
        'plan_id',
        'claims_modal',
        'roadside_inspections',
        'drug_alcohol_testing',
        'mvr_ordering',
        'bulk_driver_import',
        'der_last_name',
        'der_address',
        'der_alternative_phone'
    ];

    protected $casts = [
        'status' => 'string',
        'features_overrides' => 'array',
        'custom_forms' => 'array',
        'plan_start_date' => 'date',
        'claims_modal' => 'boolean',
        'roadside_inspections' => 'boolean',
        'drug_alcohol_testing' => 'boolean',
        'mvr_ordering' => 'boolean',
        'bulk_driver_import' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user', 'company_id', 'user_id');
    }
    public function drivers(){
        return $this->hasMany(Driver::class);
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
    public function managers()
    {
        return $this->belongsToMany(User::class, 'company_user', 'company_id', 'user_id')
            ->where('role_id', 3);
    }
    public function files(){
        return $this->hasMany(CompanyFile::class);
    }
}
