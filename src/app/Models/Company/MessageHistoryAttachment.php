<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class MessageHistoryAttachment extends Model
{
    protected $fillable = [
        'message_history_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
    ];

    public function message()
    {
        return $this->belongsTo(
            MessageHistory::class,
            'message_history_id'
        );
    }
}
