<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Driver extends Authenticatable
{
    use HasApiTokens;
    protected $fillable = [
        'primary_phone',
        'rand_number',
        'driver_temp_token',
        'phone_confirm_sent',
        'status',
        'first_name',
        'middle_name',
        'last_name',
        'ssn_sin',
        'date_of_birth',
    ];
    protected $hidden = [
        'driver_temp_token','rand_number','phone_confirm_at','phone_confirm_sent','ssn_sin'
    ];
    protected $casts = [
        'status' => 'boolean',
        'date_of_birth' => 'date',
        'phone_confirm_sent' => 'datetime',
    ];
    public function personal_information()
    {
        return $this->hasOne(PersonalInformation::class);
    }

    public function endorsement()
    {
        return $this->hasOne(Endorsement::class);
    }
    public function general_information()
    {
        return $this->hasOne(GeneralInformation::class);
    }
    public function driving_experiences(){
        return $this->hasMany(DrivingExperience::class);
    }
    public function employer_information()
    {
        return $this->hasMany(EmployerInformation::class);
    }
    public function driverSign(){
        return $this->hasOne(DriverSign::class);
    }
    public function addresses()
    {
        return $this->hasMany(DriverAddress::class);
    }
    public function licenses()
    {
        return $this->hasMany(LicenseDetail::class);
    }
    public function license()
    {
        return $this->hasOne(LicenseDetail::class)->where('current', true);
    }
    public function meds()
    {
        return $this->hasMany(MedDetail::class);
    }
    public function med(){
        return $this->hasOne(MedDetail::class)->where('current', true);
    }
    public function drugTestes()
    {
        return $this->hasMany(DrugTest::class);
    }
    public function drugTest()
    {
        return $this->hasOne(DrugTest::class)->latestOfMany();
    }



}
