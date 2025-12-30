<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class EmploymentVerificationEvent extends Model
{
    protected $fillable = [
        'employment_verification_id',
        'type',
        'method',
        'note',
        'created_by',
    ];

    public function verification()
    {
        return $this->belongsTo(EmploymentVerification::class);
    }
}
