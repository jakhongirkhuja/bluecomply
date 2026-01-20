<?php

namespace App\Models\Company;

use App\Models\Driver\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MessageHistory extends Model
{
    protected $fillable = [
        'company_id',
        'driver_id',
        'sender_id',
        'message',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function attachments()
    {
        return $this->hasMany(
            MessageHistoryAttachment::class,
            'message_history_id'
        );
    }
}
