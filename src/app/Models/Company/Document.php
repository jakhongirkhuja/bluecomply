<?php

namespace App\Models\Company;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id','document_type_id','file_name','file_path',
        'status','expires_at','uploaded_by','is_encrypted','notes'
    ];

    protected $casts = ['expires_at'=>'date'];

    public function type() {
        return $this->belongsTo(DocumentType::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
