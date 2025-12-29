<?php

namespace App\Models\Company;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'driver_id',
        'assigned_by',
        'assigned_to',
        'title',
        'description',
        'category',
        'status',
        'related_type',
        'related_id',
        'due_date',
        'priority',
    ];
    protected $casts = [
        'driver_id' => 'integer',
        'due_date' => 'date',
    ];
    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }
    public function assigneed()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
