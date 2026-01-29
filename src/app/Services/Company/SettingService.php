<?php

namespace App\Services\Company;

use App\Models\Company\Company;
use App\Models\Company\MvrMonitoring;
use App\Models\Company\NotificationSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SettingService
{
    public function postUserInformation($data,$company,$user){
        try {
            return DB::transaction(function () use ($data, $company,$user) {
                if(isset($data['password'])){
                    if(Hash::check($data['current_password'], $user->password)){
                        $user->password = Hash::make($data['password']);
                        $user->save();
                    }
                }
                $company->update([
                    'name'=>$data['company_name'],
                    'dot_number'=>$data['dot_number'],
                ]);

                $user->update([
                    'email'=>$data['email'],
                    'address'=>$data['address'],
                    'city'=>$data['city'],
                    'state_id'=>$data['state_id'],
                    'zip_code'=>$data['zip_code'],
                    'sms_2fa_enabled'=>$data['sms_2fa_enabled'],
                    'totp_enabled'=>$data['totp_enabled'],
                ]);
                return response()->success($user);
            });
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->error('Something went wrong');
        }
    }
    public function postDerInformation($data,$company_id)
    {
        $company = Company::findorfail($company_id);
        try {
            $company->update($data);
            return response()->success($company);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->error('Something went wrong');
        }

    }
    public function saveNotificationSettings($data,$company_id)
    {
        foreach ($data['notifications'] as $type => $channels) {
            $notifcation = NotificationSetting::updateOrCreate(
                ['user_id' => Auth::id(), 'type' => $type],
                ['channels' => $channels]
            );
        }
        return response()->success($notifcation);
    }
    public function mvrPostDrivers($data,$company_id)
    {

        $monitors = [];
        foreach ($data['driver_ids'] as $driverId) {
           $monitors[]=   MvrMonitoring::updateOrCreate(
                [
                    'driver_id' => $driverId,
                    'company_id' => $company_id,
                ],
                [
                    'enrolled' => true,
                ]
            );
        }
        return response()->json($monitors);
    }
    public function generalSettings($data,$company_id){
        $user = DB::transaction(function () use ($data, $company_id) {
            $user = User::find(Auth::id());
            $user->appearance = $data['appearance'];
            $user->time_zone = $data['time_zone'];
            $user->language = $data['language'];
            $user->date_format = $data['date_format'];
            $user->time_format = $data['time_format'];

            if(isset($data['logo'])){
                $file = $data['logo'];
                $path = $file->storeAs(
                    'company-logo',
                    Str::orderedUuid().rand(1,500).'.'.$file->getClientOriginalExtension(),
                    'public'
                );
                $company = Company::find($company_id);
                if($company){
                    $company->logo = $path;
                    $company->save();
                }

            }
            if(isset($data['signature'])){
                $file = $data['signature'];
                $path = $file->storeAs(
                    'user-signature',
                    Str::orderedUuid().rand(1,500).'.'.$file->getClientOriginalExtension(),
                    'public'
                );
                $user->signature = $path;
            }
            $user->save();
        });
        return response()->success($user);
    }
}
