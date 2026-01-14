<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = [
        'name','short','category_id','requires_review','is_required','requires_expiry'
    ];

}
