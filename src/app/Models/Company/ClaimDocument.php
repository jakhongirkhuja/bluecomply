<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class ClaimDocument extends Model
{
    protected $fillable = [
        'claim_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
    ];
}
