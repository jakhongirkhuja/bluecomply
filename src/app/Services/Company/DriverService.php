<?php

namespace App\Services\Company;

use App\Models\Company\Company;
use App\Models\Driver\Driver;
use App\Models\Driver\Endorsement;
use App\Models\Driver\LicenseDetail;
use App\Models\Driver\Termination;
use App\Models\Driver\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DriverService
{
    public function addDriver($data, $request)
    {
        return match ($data['type']) {
            'information' => $this->postPersonalInformation($request),
            'license' => $this->postLicense($request),
            'address' => $this->postAddress($request),
            'files' => $this->postFiles($request),
            default => throw new \InvalidArgumentException('Invalid application type'),
        };
    }

    protected function postPersonalInformation(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'email' => 'nullable|email',
            'last_name' => 'required|string',
            'ssn_sin' => 'nullable|string|unique:personal_information,ssn_sin',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'application_date' => 'required|date_format:Y-m-d',
            'med_expire_date' => 'required|date_format:Y-m-d',
            'company_code' => 'required|string',
            'position_dot' => 'required|numeric|between:0,1',
        ]);

        return DB::transaction(function () use ($data) {

            $company = Company::where('user_id', Auth::id())->firstorfail();

            $driver = Driver::create([
                'ssn_sin' => $data['ssn_sin'] ?? null,
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'date_of_birth' => $data['date_of_birth'],
                'position_dot' => $data['position_dot'],
                'company_id' => $company->id
            ]);
            $driver->personal_information()->create([
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'date_of_birth' => $data['date_of_birth'],
            ]);

            $driver->med()->create([
                'med_expiration' => $data['med_expire_date'],
            ]);
            $driver->employerInformationSingle()->create([
                'code' => $data['company_code'],
            ]);

            return $driver->employee_id;
        });
    }

    protected function postLicense(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:drivers,employee_id',
            'type_id' => 'required|string|in:cdl,dl,stateId',
            'license_type' => 'nullable|required_if:type_id,cdl',
            'license_number' => 'required',
//            'state_id' => 'required|exists:states,id',
            'expire_date' => 'required|date_format:Y-m-d',
            'restrictions' => 'nullable|array',
            'restrictions.*' => 'string',
            'endorsements' => 'required|array',
            'endorsements.*' => 'string',
            'twic_card' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10048',
        ]);

        $data['state_id'] = 1;
        return DB::transaction(function () use ($data, $request) {
            $driver = Driver::where('employee_id', $data['employee_id'])->first();
            $endorsement = Endorsement::create(['driver_id' => $driver->id, 'endorsements' => $data['endorsements']]);
            if ($request->hasFile('twic_card')) {
                if ($endorsement->twic_card_path && Storage::disk('public')->exists($endorsement->twic_card_path)) {
                    Storage::disk('public')->delete($endorsement->twic_card_path);
                }
                $path = $request->file('twic_card')->store('twic_cards', 'public');
                $endorsement->twic_card_path = $path;
                $endorsement->save();
            }
            LicenseDetail::create([
                'driver_id' => $driver->id,
                'type' => $data['type_id'],
                'license_type' => isset($data['license_type']) ? $data['license_type'] : null,
                'license_number' => $data['license_number'],
                'state_id' => $data['state_id'],
                'license_expiration' => $data['expire_date'],

            ]);
            return $driver->employee_id;
        });

    }

    protected function postAddress(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:drivers,employee_id',
//            'country_id' => 'required|string|exists:countries,id',
            'address' => 'required|string',
            'house' => 'required|string',
//            'city_id' => 'required|string|exists:cities,id',
//            'state_id' => 'required|string|exists:states,id',
            'zip' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);
        $data['country_id']= 1;
        $data['city_id'] =1;
        $data['state_id'] = 1;
        return DB::transaction(function () use ($data) {
            $driver = Driver::where('employee_id', $data['employee_id'])->first();
            $driver->address()->create([
                'country_id' => $data['country_id'],
                'address' => $data['address'],
                'house' => $data['house'],
                'city_id' => $data['city_id'],
                'state_id' => $data['state_id'],
                'zip' => $data['zip'],
                'currently_live' => true
            ]);
            $driver->personal_information()->update([
                'email' => $data['email'],
                'email_confirmed' => true,
            ]);

            $driver->primary_phone = $data['phone'];
            $driver->save();
            return $driver->employee_id;
        });
    }

    public function drivers_change_status($data)
    {
        Driver::where('id', $data['driver_id'])->update(['status' => $data['status']]);
        Termination::where('driver_id', $data['driver_id'])->delete();
        return 'Status changed to ' . $data['status'];
    }

    public function drivers_change_profile($data)
    {
        Driver::where('id', $data['driver_id'])
            ->update(Arr::except($data, ['driver_id', 'truck_name', 'truck_number']));
        $truck = Truck::updateOrCreate(
            ['driver_id' => $data['driver_id']], // search criteria
            [
                'name' => $data['truck_name'],  // columns in trucks table
                'number' => $data['truck_number'],
            ]
        );
        return 'Profile updated';
    }

}
