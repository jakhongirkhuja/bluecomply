<?php

namespace App\Models\Registration;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class RegistrationLink extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'status',
        'purpose',
        'driver_id',
        'company_id',
        'driver_token'
    ];
    protected $casts = [
        'expires_at' => 'datetime',
        'status'     => 'boolean',
    ];
    protected $hidden = [
        'user_id',
        'company_id',
        'driver_id',
        'driver_token',
    ];
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->token ??= (string) Str::uuid();
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getLinkAttribute(): ?string
    {
        $params = [];
//        if ($this->driver_id) {
//            $params['driver_id'] = $this->driver_id;
//        }
//        if ($this->company_id) {
//            $params['company_id'] = $this->company_id;
//        }
        if ($this->purpose) {
          $params['purpose'] = $this->purpose;
        }
        if ($this->driver_token) {
            $params['driver_token'] = $this->driver_token;
        }
        $params['token'] = $this->token;

//        $json = json_encode($params);
//        $base64 = base64_encode($json);
        $base64 = http_build_query($params);
        return  url('/').'/api/v1/driver/'.$base64;
    }
}
