<?php

namespace App\Services\Driver;

use App\Http\Requests\Driver\DriverTypeCheckRequest;
use App\Models\Driver\Application;
use App\Models\Driver\Driver;
use App\Models\Driver\DriverAddress;
use App\Models\Driver\DriverSign;
use App\Models\Driver\DrivingExperience;
use App\Models\Driver\EmployerInformation;
use App\Models\Driver\Endorsement;
use App\Models\Driver\GeneralInformation;
use App\Models\Driver\LicenseDetail;
use App\Models\Driver\LinkVerification;
use App\Models\Driver\MedDetail;
use App\Models\Driver\PersonalInformation;
use App\Models\Registration\RegistrationLink;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ApplicationService
{
    public function getTypeDetails($type)
    {
        return match ($type) {
            'information' => $this->getDriverInformation(),
            'details' => $this->getDriverDetail(),
            default => throw new \InvalidArgumentException('Invalid application type'),
        };
    }
    protected function getDriverInformation()
    {

        $driver = Auth::guard('driver')->user()->load([
            'personal_information',
            'addresses',
            'licenses',
            'currentLicense',
            'endorsement',
            'general_information'
        ]);
        $data['personal_information'] = $driver->personal_information;
        $data['addresses'] = $driver->addresses;
        $data['licenses'] = $driver->licenses->map(function ($license) {
            return [
                'id' => $license->id,
                'license_number' => $license->license_number,
                'issue_date' => $license->license_issue_date,
                'expiration' => $license->license_expiration,
                'current' => $license->current,
                'front_path' => $license->front_full_path,
                'back_path' => $license->back_full_path,
            ];
        });
        $data['endorsements'] = $driver->endorsement;
        $data['general_information'] = $driver->general_information;
        return response()->success($data, Response::HTTP_OK);
    }

    public function getDriverDetail()
    {
        $driver = Auth::guard('driver')->user()->load([
            'driving_experiences',
            'employer_information',
            'driverSign'
        ]);
        $data['experiences'] = $driver->driving_experiences;
        $data['engagement'] = $driver->employer_information;
        $data['driver_sign'] = $driver->driverSign ? $driver->driverSign->full_path : null;
        return response()->success($data, Response::HTTP_OK);
    }

    public function postTypeDetails($data, $request)
    {

        $driver = Auth::guard('driver')->user();

        return match ($data['type']) {
            'information' => $this->postPersonalInformation($request, $driver),
            'address' => $this->postAddress($request, $driver),
            'license' => $this->postLicense($request, $driver),
            'endorsements' => $this->postEndorsement($request, $driver),
            'general_information' => $this->postGeneralInformation($request, $driver),
            'driving_experiences' => $this->postDrivingExperience($request, $driver),
            'engagements' => $this->postEmployerInformation($request, $driver),
            'sign' => $this->postDriverSign($request, $driver),
            'files' => $this->postFiles($request, $driver),
            default => throw new \InvalidArgumentException('Invalid application type'),
        };
    }

    protected function postPersonalInformation(Request $request, $driver)
    {
        $personalInformation = PersonalInformation::where('driver_id', $driver->id)->first();

        $data = $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'email' => 'nullable|email',
            'last_name' => 'required|string',
            'ssn_sin' => [
                'required',
                'string',
                Rule::unique('personal_information', 'ssn_sin')
                    ->ignore(optional($personalInformation)->id)
            ],
            'date_of_birth' => 'required|date_format:Y-m-d',
        ]);

        return DB::transaction(function () use ($personalInformation, $data, $driver) {
            if ($personalInformation) {
                $personalInformation->update($data);
                $driver->first_name = $data['first_name'];
                $driver->middle_name = $data['middle_name'];
                $driver->last_name = $data['last_name'];
                $driver->date_of_birth = $data['date_of_birth'];
                $driver->save();
            } else {
                $data['driver_id'] = $driver->id;
                $personalInformation = PersonalInformation::create($data);
                $driver->first_name = $data['first_name'];
                $driver->middle_name = $data['middle_name'];
                $driver->last_name = $data['last_name'];
                $driver->date_of_birth = $data['date_of_birth'];
                $driver->save();
            }
            return response()->success($personalInformation, Response::HTTP_CREATED);
        });
    }

    protected function postAddress(Request $request, $driver)
    {
        $data = $request->validate([
            'id' => 'nullable|exists:driver_addresses,id',
            'address' => 'required|string',
            'move_in' => 'required|date_format:Y-m-d',
            'move_out' => 'nullable|required_if:currently_live,0|date_format:Y-m-d|after_or_equal:move_in',
            'currently_live' => 'required|numeric|between:0,1',
        ]);

        return DB::transaction(function () use ($data, $driver) {

            if (!empty($data['id'])) {
                $driverAddress = DriverAddress::where('id', $data['id'])
                    ->where('driver_id', $driver->id)
                    ->first();
                $driverAddress->update($data);
            } else {
                $driverAddress = DriverAddress::create([
                    'driver_id' => $driver->id,
                    ...$data
                ]);
            }
            return response()->success($driverAddress, Response::HTTP_OK);
        });
    }

    protected function postLicense(Request $request, $driver)
    {
        $data = $request->validate([
            'id' => 'nullable|exists:license_details,id',
            'license_number' => 'required|string|max:150',
            'license_issue_date' => 'required|date_format:Y-m-d',
            'license_expiration' => 'required|date_format:Y-m-d|after_or_equal:license_issue_date',
//            'state_id' => 'required|exists:states,id',
        ]);
        return DB::transaction(function () use ($data, $driver) {
            $data['state_id'] = 1;
            if (!empty($data['id'])) {
                $licenseDetail = LicenseDetail::where('id', $data['id'])
                    ->where('driver_id', $driver->id)
                    ->firstOrFail();
                $licenseDetail->update($data);
            } else {

                LicenseDetail::where('driver_id', $driver->id)
                    ->update(['current' => false]);

                $licenseDetail = LicenseDetail::create([
                    'driver_id' => $driver->id,
                    ...$data
                ]);
            }
            return response()->success($licenseDetail, Response::HTTP_OK);
        });
    }

    protected function postEndorsement(Request $request, $driver)
    {
        $data = $request->validate([
            'endorsements' => 'required|array',
            'endorsements.*' => 'string',
            'twic_card' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10048',
        ]);

        return DB::transaction(function () use ($data, $driver, $request) {
            $endorsement = Endorsement::firstOrNew(['driver_id' => $driver->id]);

            if ($request->hasFile('twic_card')) {

                if ($endorsement->twic_card_path && Storage::disk('public')->exists($endorsement->twic_card_path)) {
                    Storage::disk('public')->delete($endorsement->twic_card_path);
                }
                $path = $request->file('twic_card')->store('twic_cards', 'public');
                $data['twic_card_path'] = $path;
            }

            $data['driver_id'] = $driver->id;

            $endorsement = Endorsement::updateOrCreate(
                ['driver_id' => $driver->id],
                $data
            );
            return response()->success($endorsement, Response::HTTP_OK);
        });
    }

    protected function postGeneralInformation(Request $request, $driver)
    {
        $data = $request->validate([
            'license_denial' => 'required|numeric|between:0,1',
            'has_driving_convictions' => 'required|numeric|between:0,1',
            'has_substance_conviction' => 'required|numeric|between:0,1',
            'positive_substance_violation' => 'required|numeric|between:0,1',
            'has_moving_violation_or_accident_last_3_years' => 'required|numeric|between:0,1',
            'has_violations_accidents' => 'required|numeric|between:0,1',
            'eligible_for_us_employment' => 'required|numeric|between:0,1',
            'speak_english' => 'required|numeric|between:0,1',
        ]);
        return DB::transaction(function () use ($data, $driver) {
            $data['driver_id'] = $driver->id;
            $generalInfo = GeneralInformation::updateOrCreate(
                ['driver_id' => $driver->id],
                $data
            );
            return response()->success($generalInfo, Response::HTTP_OK);
        });
    }

    protected function postDrivingExperience(Request $request, $driver)
    {
        $data = $request->validate([
            'id' => 'nullable|exists:driving_experiences,id',
            'years_of_experience' => 'required|integer|min:0|max:70',
            'miles_driven' => 'required|integer|min:0',
            'from' => 'required|date_format:Y-m-d|before_or_equal:to',
            'to' => 'required|date_format:Y-m-d|after_or_equal:from',
            'equipment_operated' => 'nullable|array',
            'equipment_operated.*' => 'string',
//            'state_id' => 'required|exists:states,id',
        ]);

        $data['state_id'] = 1;
        return DB::transaction(function () use ($data, $driver) {

            if (!empty($data['id'])) {
                $experience = DrivingExperience::where('id', $data['id'])
                    ->where('driver_id', $driver->id)
                    ->firstOrFail();
                $experience->update($data);
            } else {
                $data['driver_id'] = $driver->id;
                $experience = DrivingExperience::create([
                    'driver_id' => $driver->id,
                    ...$data
                ]);
            }
            return response()->success($experience, Response::HTTP_OK);
        });
    }

    protected function postEmployerInformation(Request $request, $driver)
    {

        $data = $request->validate([
            'id' => 'nullable|exists:employer_information,id',
            'type_engagement' => 'required|string|in:job,military,education,unemployment',
            'name' => 'required|string',
            'position' => 'nullable|string',
            'address' => 'required|string',
            'start_date' => 'required|date_format:Y-m-d|before_or_equal:end_date',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'current_employer' => 'nullable|required_if:type_engagement,job|numeric|between:0,1',
            'reason_for_leaving' => 'nullable|required_if:current_employer,0|string',
            'company_contact_name' => 'nullable|required_if:type_engagement,job|string',
            'company_contact_phone' => 'nullable|required_if:type_engagement,job|string',
            'company_contact_email' => 'nullable|email',
            'company_contact_allow' => 'nullable|required_if:type_engagement,job|numeric|between:0,1',
            'safety_regulations' => 'nullable|required_if:type_engagement,job|numeric|between:0,1',
            'sensitive_functions' => 'nullable|required_if:type_engagement,job|numeric|between:0,1',
            'motor_vehicle' => 'nullable|required_if:type_engagement,job|numeric|between:0,1',
            'work_type' => [
                'sometimes',
                'required_if:type_engagement,job',
                'in:local,regional,otr,drayage',
            ],
            'equipment_operated' => 'nullable|required_if:type_engagement,job|array',
            'equipment_operated.*' => 'string',
        ]);

        return DB::transaction(function () use ($data, $driver) {
            $data['type']=$data['work_type'];
            $data['driver_id'] = $driver->id;
            if (!empty($data['id'])) {
                $employer = EmployerInformation::where('id', $data['id'])
                    ->where('driver_id', $driver->id)
                    ->firstOrFail();

                $employer->update($data);
            } else {
                if (!empty($data['current_employer'])) {
                    EmployerInformation::where('driver_id', $driver->id)
                        ->where('current_employer', true)
                        ->update(['current_employer' => false]);
                }
                $employer = EmployerInformation::create([
                    'driver_id' => $driver->id,
                    ...$data
                ]);
            }
            return response()->success($employer, Response::HTTP_OK);
        });
    }

    protected function postDriverSign(Request $request, $driver)
    {
        $data = $request->validate([
            'sign' => 'required|file|mimes:png,jpg,jpeg,pdf|max:2048',
            'checked' => 'nullable|numeric|in:1',
        ]);

        return DB::transaction(function () use ($data, $driver, $request) {

            $driverSign = DriverSign::firstOrNew(['driver_id' => $driver->id]);

            if ($request->hasFile('sign')) {
                if ($driverSign->sign_path && Storage::disk('public')->exists($driverSign->sign_path)) {
                    Storage::disk('public')->delete($driverSign->sign_path);
                }

                $path = $request->file('sign')->store('driver_signs', 'public');
                $data['sign_path'] = $path;
                $application = Application::where('driver_id', $driver->id)->first();
                if (!$application) {
                    $application = Application::create([
                        'driver_id' => $driver->id,
                        'confirmation_number' => Str::upper(Str::random(5)) . '-' . Str::upper(Str::random(4)),
                    ]);
                }
                $application->update([
                    'submitted' => true,
                    'used_at' => now(),
                    'used_ip' => $request->ip(),
                ]);
            }
            $driverSign->fill($data);
            $driverSign->save();
            return response()->success($driverSign, Response::HTTP_OK);
        });
    }

    protected function postFiles(Request $request, $driver)
    {
        $data = $request->validate([
            'file_type' => 'required|string|in:license_front,license_back,medical',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        return DB::transaction(function () use ($data, $driver, $request) {

            $file = $request->file('file');
            $folder = match($data['file_type']) {
                'license_front' => 'licenses/front',
                'license_back'  => 'licenses/back',
                'medical'       => 'medical',
            };

            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs($folder, $filename, 'public');

            switch ($data['file_type']) {
                case 'license_front':
                case 'license_back':
                    $column = $data['file_type'] === 'license_front'
                        ? 'driver_license_front_path'
                        : 'driver_license_back_path';

                    $licence = LicenseDetail::where('driver_id', $driver->id)
                        ->where('current', true)
                        ->first();
                    if (!$licence) {
                        $licence = LicenseDetail::where('driver_id', $driver->id)->latest()->first();
                        if ($licence) $licence->current = true;
                    }
                    if ($licence) {
                        $licence->$column = $path;
                        $licence->save();
                    }
                    break;
                case 'medical':

                    $med = MedDetail::where('driver_id', $driver->id)
                        ->where('current', true)
                        ->first();
                    if (!$med) {
                        $med = MedDetail::where('driver_id', $driver->id)->latest()->first();
                        if ($med) {
                            $med->current = true;
                        }
                    }
                    if (!$med) {
                        $med = new MedDetail();
                        $med->driver_id = $driver->id;
                        $med->current = true;
                    }
                    $med->med_path = $path;
                    $med->save();
                    break;
            }

            return response()->success([
                'file_type' => $data['file_type'],
                'path' => $path,
            ], Response::HTTP_OK);
        });
    }

    public function driverLogin($data)
    {
        try {
            $driver = Driver::updateOrCreate(
                ['primary_phone' => $data['primary_phone']], // search criteria
                [
                    'rand_number' => rand(1000, 9999),
                    'phone_confirm_sent' => Carbon::now(),
                    'phone_confirm_at' => null,
                ]
            );
            return response()->success($driver, Response::HTTP_CREATED);
            return response()->success('Message has been sent', Response::HTTP_CREATED);

        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()->error(
                $e instanceof QueryException ? $e->getMessage() : 'Internal server error',
                $e instanceof QueryException ? 400 : 500
            );
        }
    }

    public function driverLoginConfirm($data)
    {
        try {
            $driver = Driver::where('primary_phone', $data['primary_phone'])->first();
            $codeValid = $driver
                && $driver->rand_number == $data['rand_number']
                && $driver->phone_confirm_sent
                && $driver->phone_confirm_sent->greaterThan(now()->subMinutes(5));

            if ($codeValid) {
                $driver->update(['phone_confirm_at' => now(), 'phone_confirm_sent' => null,'hired_at'=>now()]);
                $token = $driver->createToken('driver-token')->plainTextToken;
                $company = RegistrationLink::where('token',  $data['company_token'])->first();
                $linkVerification = LinkVerification::where('link_id', $company->id)->where('driver_id', $driver->id)->first();
                if (!$linkVerification) {
                    LinkVerification::create(['link_id' => $company->id, 'driver_id' => $driver->id]);
                }
                return response()->success([
                    'token' => $token
                ]);
            }
            return response()->error('Invalid or expired code', 400);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()->error(
                $e instanceof QueryException ? $e->getMessage() : 'Internal server error',
                $e instanceof QueryException ? 400 : 500
            );
        }
    }

    public function applicationStatus($type)
    {
        $data = null;
        if($type=='general'){
            $user_id = Auth::guard('driver')->id();
            $licence = LicenseDetail::where('driver_id', $user_id)->where('current',true)->first();
            $med = MedDetail::where('driver_id', $user_id)->first();
            $aplication = Application::where('driver_id', $user_id)->first();

            $data['info']['dot_applicaton'] = false;
            $data['files']['licence'] = false;
            $data['files']['med'] = false;
            if ($licence?->driver_license_front_path) {
                $data['files']['licence'] = true;
            }
            if ($med?->med_path) {
                $data['files']['med'] = true;
            }
            if ($aplication?->submitted) {
                $data['info']['dot_applicaton'] = true;
            }
        }elseif ($type=='inside'){
            $data['personal_information'] = PersonalInformation::where('driver_id',$user_id)->exists();
            $data['compliance_check'] = GeneralInformation::where('driver_id',$user_id)->exists();;
            $data['professional_history'] = DrivingExperience::where('driver_id',$user_id)->exists();
        }
        return response()->success($data, Response::HTTP_OK);
    }
}
