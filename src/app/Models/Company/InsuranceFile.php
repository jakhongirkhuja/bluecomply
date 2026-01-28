<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class InsuranceFile extends Model
{
    protected $fillable = [
        'company_id',
        'insurance_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];
    protected static function booted()
    {
        static::creating(function ($file) {
            if (auth()->check()) {
                $file->uploaded_by = auth()->id();
            }
        });

    }
}
