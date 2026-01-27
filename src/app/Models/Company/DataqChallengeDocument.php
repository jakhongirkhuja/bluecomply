<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class DataqChallengeDocument extends Model
{
    protected $fillable = [
        'dataq_challenge_id',
        'company_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
    ];
    public function challenge()
    {
        return $this->belongsTo(DataqChallenge::class, 'dataq_challenge_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
