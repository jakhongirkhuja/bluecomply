<?php

namespace App\Models\Company;

use App\Models\Driver\Driver;
use App\Models\User;
use App\Services\Company\GlobalDocumentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Document extends Model
{
    protected $fillable = [
        'company_id',
        'user_id','document_type_id','file_name','file_path','side','driver_id','current','name','number','cdl_class_id',
        'status','expires_at','issue_at','uploaded_by','is_encrypted','notes','category_id','state_id','class',
    ];

    protected $casts = ['expires_at'=>'date'];

    public function files()
    {
        return $this->hasMany(DocumentFile::class);
    }
    public function type() {
        return $this->belongsTo(DocumentType::class,'document_type_id','id');
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

    protected static function booted()
    {
        static::created(function (Document $document) {
            $driver = $document->driver;
            if (auth()->guard('driver')->check()) {
                $uploaded_by_table_name = 'driver';
            } elseif (auth()->check()) {
                $uploaded_by_table_name = 'users';
            }

            app(GlobalDocumentService::class)->sync(
                $document,
                [
                    'company_id' => $document->company_id,
                    'category'   => 'Driver',
                    'type'       => $document->type->name,
                    'name'       =>$driver->first_name.' - '.$document->type->name,
                    'related_to' => $driver->first_name.' '.$driver->last_name,
                    'expiration' => $document->expires_at,
                    'uploaded_by_id'=> auth()->id(),
                    'uploaded_by_table_name'=>$uploaded_by_table_name
                ]
            );

        });

        static::updated(function (Document $document) {
            $driver = $document->driver;
            if (auth()->guard('driver')->check()) {
                $uploaded_by_table_name = 'driver';
            } elseif (auth()->check()) {
                $uploaded_by_table_name = 'users';
            }
            app(GlobalDocumentService::class)->sync(
                $document,
                [
                    'company_id' => $document->company_id,
                    'category'   => 'Driver',
                    'type'       => $document->type->name,
                    'name'       =>$driver->first_name.' - '.$document->type->name,
                    'related_to' => $driver->first_name.' '.$driver->last_name,
                    'expiration' => $document->expires_at,
                    'uploaded_by_id'=> auth()->id(),
                    'uploaded_by_table_name'=>$uploaded_by_table_name
                ]
            );
        });

        static::deleted(function (Document $document) {
            app(GlobalDocumentService::class)->delete($document);
        });
    }

}
