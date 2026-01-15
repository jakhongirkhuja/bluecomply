<?php

namespace App\Models\Company;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
