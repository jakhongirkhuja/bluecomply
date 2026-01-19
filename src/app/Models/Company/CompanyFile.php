<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class CompanyFile extends Model
{
    protected $fillable = [
        'company_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
    ];

    public function document()
    {
        return $this->belongsTo(CompanyFile::class);
    }
}
