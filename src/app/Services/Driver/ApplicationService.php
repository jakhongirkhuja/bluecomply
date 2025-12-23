<?php

namespace App\Services\Driver;
use App\Http\Requests\Driver\DriverTypeCheckRequest;
use App\Models\Driver\Application;
use App\Models\Driver\Driver;
use App\Models\Driver\LicenseDetail;
use App\Models\Driver\LinkVerification;
use App\Models\Driver\MedDetail;
use App\Models\Driver\PersonalInformation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\QueryException;
class ApplicationService
{
    public function getTypeDetails($type)
    {
        return match ($type) {
            'information' => $this->getDriverInformation(),
            'details' => $this->getDriverDetail(),
            default                => throw new \InvalidArgumentException('Invalid application type'),
        };
    }
    public function postTypeDetails(DriverTypeCheckRequest $request)
    {
        return match ($request->only('type')) {
            'information' => $this->postPersonalInformation($request),
            'address' => $this->postAddress($request),
            default                => throw new \InvalidArgumentException('Invalid application type'),
        };
    }
    public function getDriverDetail(){
        $driver = Auth::guard('driver')->user()->load([
            'driving_experiences',
            'employer_information',
            'driverSign'
        ]);
        $data['experiences']   = $driver->driving_experiences;
        $data['engagement']   = $driver->employer_information;
        $data['driver_sign']   =$driver->driverSign ? $driver->driverSign->full_path : null;
        return response()->success($data, Response::HTTP_OK);
    }
    protected function getDriverInformation(){

        $driver = Auth::guard('driver')->user()->load([
            'personalInformation',
            'addresses',
            'licenses',
            'currentLicense',
            'endorsement',
            'general_information'
        ]);
        $data['personal_information']   = $driver->personal_information;
        $data['addresses']      = $driver->addresses;
        $data['licenses'] = $driver->licenses->map(function($license) {
            return [
                'id'             => $license->id,
                'license_number' => $license->license_number,
                'issue_date'     => $license->license_issue_date,
                'expiration'     => $license->license_expiration,
                'current'        => $license->current,
                'front_path'     => $license->front_full_path,
                'back_path'      => $license->back_full_path,
            ];
        });
        $data['endorsements']   = $driver->endorsement;
        $data['general_information']   = $driver->general_information;
        return response()->success($data, Response::HTTP_OK);
    }
    protected function postPersonalInformation(Request $request)
    {
        $personalInformation = PersonalInformation::where('driver_id', Auth::id())->first();

        $data = $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'ssn_sin' => 'required|string|unique:personal_information,ssn_sin,' . optional($personalInformation)->id,
            'date_of_birth' => 'required|date',
        ]);
        return DB::transaction(function () use ($personalInformation,$data) {
            if($personalInformation){
                $personalInformation->update($data);
            }else{
                $personalInformation= PersonalInformation::create($data);
            }
            return response()->success($personalInformation, Response::HTTP_CREATED);
        });
    }
    protected function postAddress(Request $request)
    {
        $data = $request->validate([
            'address' => 'required|string',
            'move_in' => 'required|string',
            'move_out' => 'nullable|integer',
            'currently_live' => 'nullable'
        ]);

        return DB::transaction(function () use ($application, $data) {
            $record = $application->address()->updateOrCreate(
                ['application_id' => $application->id],
                array_merge($data, [
                    'driver_id' => $application->driver_id,
                ])
            );

            $application->update(['step' => 3]);

            return $record;
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
    public function driverLoginConfirm($data){
        try {
            $driver = Driver::where('primary_phone', $data['primary_phone'])->first();
            $codeValid = $driver
                && $driver->rand_number == $data['rand_number']
                && $driver->phone_confirm_sent
                && $driver->phone_confirm_sent->greaterThan(now()->subMinutes(5));

            if ($codeValid) {
                $driver->update([ 'phone_confirm_at' => now(), 'phone_confirm_sent' => null]);
                $token = $driver->createToken('driver-token')->plainTextToken;
                $linkVerification = LinkVerification::where('link_id', $data['company_token'])->where('driver_id',$driver->id)->first();
                if(!$linkVerification){
                    LinkVerification::create(['link_id'=>$data['company_token'],'driver_id'=>$driver->id]);
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
    public function applicationStatus(){
        $user_id = Auth::id();
        $licence = LicenseDetail::where('driver_id', $user_id)->first();
        $med = MedDetail::where('driver_id', $user_id)->first();
        $aplication = Application::where('driver_id', $user_id)->first();
        $data['licence'] = false;
        $data['med'] = false;
        $data['dot_applicaton']= false;
        if($licence->driver_license_front_path){
            $data['licence'] = true;
        }
        if($med->med_path){
            $data['med'] = true;
        }
        if($aplication->submitted){
            $data['dot_applicaton'] = true;
        }
        return response()->success($data, Response::HTTP_OK);
    }
}
