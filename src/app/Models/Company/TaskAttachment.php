<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    protected $fillable = [
        'task_id',
        'file_path',
        'original_name',
    ];
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
}
