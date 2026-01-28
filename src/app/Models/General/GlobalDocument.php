<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class GlobalDocument extends Model
{
    protected $fillable = [
        'source_id',
        'source_table',
        'name',
        'category',
        'type',
        'related_to',
        'upload_date',
        'expiration',
        'company_id',
        'status',
        'uploaded_by_id',
        'uploaded_by_table_name',
    ];
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
