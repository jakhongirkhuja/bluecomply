<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class IncidentFile extends Model
{
    protected $fillable = [
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
    ];
}
