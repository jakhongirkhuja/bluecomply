<?php

namespace App\Models\Company;

use App\Models\Driver\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'driver_id',
        'document_id',
        'type',
        'title',
        'message',
        'level',   // critical / warning / info
        'status',  // unread / read
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
