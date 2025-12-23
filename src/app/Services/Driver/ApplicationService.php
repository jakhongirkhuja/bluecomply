<?php

namespace App\Services\Driver;

use App\Http\Requests\Driver\DriverTypeCheckRequest;
use App\Models\Driver\Application;
use App\Models\Driver\Driver;
use App\Models\Driver\DriverAddress;
use App\Models\Driver\DrivingExperience;
use App\Models\Driver\EmployerInformation;
use App\Models\Driver\Endorsement;
use App\Models\Driver\GeneralInformation;
use App\Models\Driver\LicenseDetail;
use App\Models\Driver\LinkVerification;
use App\Models\Driver\MedDetail;
use App\Models\Driver\PersonalInformation;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
            'personalInformation',
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

    public function postTypeDetails(DriverTypeCheckRequest $request)
    {
        $driver = Auth::guard('driver')->user();
        return match ($request->only('type')) {
            'information' => $this->postPersonalInformation($request, $driver),
            'address' => $this->postAddress($request, $driver),
            'license' => $this->postLicense($request, $driver),
            'endorsements' => $this->postEndorsement($request, $driver),
            'general_information' => $this->postGeneralInformation($request, $driver),
            'driving_experiences'=>$this->postDrivingExperience($request, $driver),
            'engagements' => $this->postEmployerInformation($request, $driver),
            default => throw new \InvalidArgumentException('Invalid application type'),
        };
    }

    protected function postPersonalInformation(Request $request, $driver)
    {
        $personalInformation = PersonalInformation::where('driver_id', $driver->id)->first();

        $data = $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'ssn_sin' => 'required|string|unique:personal_information,ssn_sin,' . optional($personalInformation)->id,
            'date_of_birth' => 'required|date',
        ]);
        return DB::transaction(function () use ($personalInformation, $data) {
            if ($personalInformation) {
                $personalInformation->update($data);
            } else {
                $personalInformation = PersonalInformation::create($data);
            }
            return response()->success($personalInformation, Response::HTTP_CREATED);
        });
    }

    protected function postAddress(Request $request, $driver)
    {
        $data = $request->validate([
            'id' => 'nullable|exists:driver_addresses,id',
            'address' => 'required|string',
            'move_in' => 'required|date_format:Y-m-d H:i:s',
            'move_out' => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:move_in',
            'currently_live' => 'nullable|numeric|between:0,1',
        ]);

        return DB::transaction(function () use ($data, $driver) {

            if (!empty($data['id'])) {
                $driverAddress = DriverAddress::where('id', $data['id'])
                    ->where('driver_id', $driver->id)
                    ->firstOrFail();
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
            'license_number' => 'required|string',
            'license_issue_date' => 'required|date_format:Y-m-d',
            'license_expiration' => 'required|date_format:Y-m-d|after_or_equal:license_issue_date',
            'state_id' => 'required|exists:states,id',
        ]);
        return DB::transaction(function () use ($data, $driver) {
            if (!empty($data['id'])) {
                $licenseDetail = LicenseDetail::where('id', $data['id'])
                    ->where('driver_id', $driver->id)
                    ->firstOrFail();
                $licenseDetail->update($data);
            } else {

                LicenseDetail::where('driver_id', $driver->id)
                    ->update(['is_current' => false]);

                $licenseDetail = DriverAddress::create([
                    'driver_id' => $driver->id,
                    ...$data
                ]);
            }
            return response()->success($licenseDetail, Response::HTTP_OK);
        });
    }
    protected function postEndorsement(Request $request, $driver){
        $data = $request->validate([
            'endorsements'     => 'nullable|array',
            'endorsements.*'   => 'string',
            'twic_card'   => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10048',
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
            $endorsement = Endorsement::updateOrCreate(
                ['driver_id' => $driver->id],
                $data
            );
            return response()->success($endorsement, Response::HTTP_OK);
        });
    }
    protected function postGeneralInformation(Request $request, $driver){
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
            'id'                   => 'nullable|exists:driving_experiences,id',
            'years_of_experience'  => 'required|integer|min:0|max:50',
            'miles_driven'         => 'required|integer|min:0',
            'from'                 => 'required|date|before_or_equal:to',
            'to'                   => 'required|date|after_or_equal:from',
            'equipment_operated'   => 'nullable|array',
            'equipment_operated.*' => 'string',
            'state_id'             => 'required|exists:states,id',
        ]);

        return DB::transaction(function () use ($data, $driver) {

            if (!empty($data['id'])) {
                $experience = DrivingExperience::where('id', $data['id'])
                    ->where('driver_id', $driver->id)
                    ->firstOrFail();
                $experience->update($data);
            } else {
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
            'type_engagement' => 'required|string',
            'name' => 'required|string',
            'position' => 'nullable|string',
            'address' => 'required|string',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'current_employer' => 'nullable|numeric|between:0,1',
            'reason_for_leaving' => 'nullable|required_if:current_employer,0|string',
            'company_contact_name' => 'nullable|string',
            'company_contact_phone' => 'nullable|string',
            'company_contact_email' => 'nullable|email',
            'company_contact_allow' => 'nullable|numeric|between:0,1',
            'safety_regulations' => 'nullable|numeric|between:0,1',
            'sensitive_functions' => 'nullable|numeric|between:0,1',
            'motor_vehicle' => 'nullable|numeric|between:0,1',
            'type' => 'nullable|string',
            'equipment_operated' => 'nullable|array',
            'equipment_operated.*' => 'string',
        ]);

        return DB::transaction(function () use ($data, $driver) {

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
//            return response()->success($driver, Response::HTTP_CREATED);
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
                $driver->update(['phone_confirm_at' => now(), 'phone_confirm_sent' => null]);
                $token = $driver->createToken('driver-token')->plainTextToken;
                $linkVerification = LinkVerification::where('link_id', $data['company_token'])->where('driver_id', $driver->id)->first();
                if (!$linkVerification) {
                    LinkVerification::create(['link_id' => $data['company_token'], 'driver_id' => $driver->id]);
                }
                return response()->success([
                    'driver_id' => $driver->id,
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

    public function applicationStatus()
    {
        $user_id = Auth::id();
        $licence = LicenseDetail::where('driver_id', $user_id)->first();
        $med = MedDetail::where('driver_id', $user_id)->first();
        $aplication = Application::where('driver_id', $user_id)->first();
        $data['licence'] = false;
        $data['med'] = false;
        $data['dot_applicaton'] = false;
        if ($licence->driver_license_front_path) {
            $data['licence'] = true;
        }
        if ($med->med_path) {
            $data['med'] = true;
        }
        if ($aplication->submitted) {
            $data['dot_applicaton'] = true;
        }
        return response()->success($data, Response::HTTP_OK);
    }
}
