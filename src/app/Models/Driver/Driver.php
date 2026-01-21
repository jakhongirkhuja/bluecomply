<?php

namespace App\Models\Driver;

use App\Models\Company\Company;
use App\Models\Company\Document;
use App\Models\Company\Incident;
use App\Models\Company\RejectionReason;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Driver extends Authenticatable
{
    use LogsActivity, HasApiTokens, HasFactory;

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
        'hired_at',
        'position_dot',
        'company_id',
        'random_pool',
        'mvr_monitor',
    ];
//    protected $hidden = [
//        'driver_temp_token', 'rand_number', 'phone_confirm_at', 'phone_confirm_sent', 'ssn_sin'
//    ];
    protected $casts = [
        'random_pool' => 'boolean',
        'mvr_monitor' => 'boolean',
        'date_of_birth' => 'date',
        'phone_confirm_sent' => 'datetime',
    ];
    protected $guarded = ['employee_id'];

    protected static function booted()
    {
        static::creating(function ($driver) {
            if (!$driver->employee_id) { // only generate if not set
                $driver->employee_id = self::generateEmployeeId();
            }
        });
    }

    public static function generateEmployeeId()
    {
        $letters = strtoupper(Str::random(2));
        $newNumber = 1000;
        $lastDriver = self::where('employee_id', 'like', $letters . '%')
            ->orderBy('id', 'desc')
            ->first();
        if ($lastDriver) {
            $lastNumber = (int)substr($lastDriver->employee_id, 2);
            $newNumber = $lastNumber + 1;
        }
        do {
            $numberPart = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            $employeeId = $letters . $numberPart;
            $newNumber++;
        } while (self::where('employee_id', $employeeId)->exists());
        return $employeeId;
    }

    public function personal_information()
    {
        return $this->hasOne(PersonalInformation::class);
    }
    public function rejections(){
        return $this->hasMany(RejectionReason::class);
    }

    public function endorsement()
    {
        return $this->hasOne(Endorsement::class);
    }
    public function company(){
        return $this->belongsTo(Company::class);
    }
    public function general_information()
    {
        return $this->hasOne(GeneralInformation::class);
    }

    public function driving_experiences()
    {
        return $this->hasMany(DrivingExperience::class);
    }

    public function employer_information()
    {
        return $this->hasMany(EmployerInformation::class);
    }

    public function employerInformationSingle()
    {
        return $this->hasOne(EmployerInformation::class);
    }

    public function driverSign()
    {
        return $this->hasOne(DriverSign::class);
    }

    public function addresses()
    {
        return $this->hasMany(DriverAddress::class);
    }

    public function address()
    {
        return $this->hasOne(DriverAddress::class)->where('currently_live',true);
    }

    public function licenses()
    {
        return $this->hasMany(Document::class);
    }

    public function license()
    {
        return $this->hasOne(Document::class)->where('document_type_id',1)->where('current', true);
    }

    public function meds()
    {
        return $this->hasMany(MedDetail::class);
    }

    public function med()
    {
        return $this->hasOne(Document::class)->where('document_type_id',4)->where('current', true);
    }

    public function drugTestes()
    {
        return $this->hasMany(DrugTest::class);
    }

    public function drugTest()
    {
        return $this->hasOne(DrugTest::class)->latestOfMany();
    }

    public function termination()
    {
        return $this->hasOne(Termination::class)->latestOfMany();
    }

    public function trucks()
    {
        return $this->hasMany(Truck::class);
    }

    public function truck()
    {
        return $this->hasOne(Truck::class)->latestOfMany();
    }

    public function employmentPeriods()
    {
        return $this->hasMany(EmploymentPeriod::class);
    }

    public function employmentVerifications()
    {
        return $this->hasMany(EmploymentVerification::class);
    }
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'driver_vehicles')
            ->withPivot(['role', 'assigned_at', 'unassigned_at', 'is_active'])
            ->withTimestamps();
    }

    public function activeTruck()
    {
        return $this->vehicles()
            ->wherePivot('role', 'Truck')
            ->wherePivot('is_active', true)
            ->first();
    }

    public function activeTrailer()
    {
        return $this->vehicles()
            ->wherePivot('role', 'Trailer')
            ->wherePivot('is_active', true)
            ->first();
    }
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
