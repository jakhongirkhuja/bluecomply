<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'action', 'details', 'user_id', 'ip_address'
    ];

    public static function log(Model $subject = null,string $action,string $details = null) {
        self::create([
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'action' => $action,
            'details' => $details,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
        ]);
    }
}
