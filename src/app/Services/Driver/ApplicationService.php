<?php

namespace App\Services\Driver;
use App\Models\Driver\Application;
use App\Models\Driver\Driver;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\QueryException;
class ApplicationService
{
    public function handleStep(Application $application, string $step, Request $request)
    {
        return match ($step) {
            'personal-information' => $this->personalInformation($application, $request),
            'address'              => $this->address($application, $request),
            default                => throw new \InvalidArgumentException('Invalid application step'),
        };
    }
    protected function personalInformation(Application $application, Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'ssn_sin' => 'required|string|unique:personal_information,ssn_sin,' .
                optional($application->personalInformation)->id,
            'date_of_birth' => 'required|date',
            'known_by_other_name' => 'boolean',
            'other_name' => 'nullable|string',
        ]);
        return DB::transaction(function () use ($application, $data) {
            $record = $application->personalInformation()->updateOrCreate(
                ['application_id' => $application->id],
                array_merge($data, [
                    'driver_id' => $application->driver_id,
                ])
            );

            $application->update(['step' => 2]);

            return $record;
        });
    }
    protected function address(Application $application, Request $request)
    {
        $data = $request->validate([
            'line1' => 'required|string',
            'line2' => 'nullable|string',
            'city_id' => 'required|integer',
            'state_id' => 'required|integer',
            'zip' => 'required|string',
            'residence_over_3_years' => 'boolean',
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
}
