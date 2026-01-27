<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class IncidentFile extends Model
{
    protected $fillable = [
        'type',
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
