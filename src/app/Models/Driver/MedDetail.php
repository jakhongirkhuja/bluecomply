<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class MedDetail extends Model
{
    protected $fillable = [
        'driver_id',
        'med_path',
        'med_issue_date',
        'med_expiration',
        'current',
    ];

    protected $casts = [
        'med_path' => 'string',
        'med_issue_date' => 'date',
        'med_expiration' => 'date',
        'current' => 'boolean',
    ];
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
