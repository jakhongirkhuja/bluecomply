<?php

namespace App\Models\Driver;

use App\Models\Company\Document;
use Illuminate\Database\Eloquent\Model;

class MedDetail extends Model
{
    protected $fillable = [
        'driver_id',
        'med_path',
        'med_issue_date',
        'med_expiration',
        'document_id',
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
    public function document() {
        return $this->belongsTo(Document::class);
    }
}
