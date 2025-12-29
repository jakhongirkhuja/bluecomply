<?php

namespace App\Models\Company;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'title',
        'user_id',
        'created_by',
        'priority',
        'show_at',
        'status',
        'driver_id',
    ];

    protected $casts = [
        'user_id' => 'array',
        'show_at' => 'datetime',
    ];
    public function users()
    {
        return User::whereIn('id', $this->user_id ?? [])->get();
    }
}
