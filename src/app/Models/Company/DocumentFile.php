<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class DocumentFile extends Model
{
    protected $fillable = [
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'side',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
