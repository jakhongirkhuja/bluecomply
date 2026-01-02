<?php

namespace App\Models\Company;

use App\Models\Driver\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id','document_type_id','file_name','file_path','side','driver_id','current','name','number','cdl_class_id',
        'status','expires_at','issue_at','uploaded_by','is_encrypted','notes','category_id','state_id','class',
    ];

    protected $casts = ['expires_at'=>'date'];

    public function files()
    {
        return $this->hasMany(DocumentFile::class);
    }
    public function type() {
        return $this->belongsTo(DocumentType::class);
    }
    public function category()
    {
        return $this->belongsTo(DocumentCategory::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function driver() {
        return $this->belongsTo(Driver::class);
    }
}
