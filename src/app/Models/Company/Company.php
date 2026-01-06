<?php

namespace App\Models\Company;

use App\Models\Admin\Plan;
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
        'custom_forms'
    ];

    protected $casts = [
        'status' => 'boolean',
        'features_overrides' => 'array',
        'custom_forms' => 'array',
        'plan_start_date' => 'date',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user', 'company_id', 'user_id');
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
}
