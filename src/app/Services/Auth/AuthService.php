<?php

namespace App\Services\Auth;

use App\Models\Company\Company;
use App\Models\Company\UserApiSession;
use App\Models\Driver\Driver;
use App\Models\Driver\EmploymentPeriod;
use App\Models\Driver\LinkVerification;
use App\Models\Registration\RegistrationLink;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    public function authLogin($data)
    {
        try {
//            $driver = Driver::where('primary_phone', $data['primary_phone'])->first();
//
//            if ($driver && $driver->phone_confirm_sent) {
//                if (Carbon::now()->diffInSeconds($driver->phone_confirm_sent) < 120) {
//                    return response()->error(
//                        'You can request a new code after 2 minutes',
//                        Response::HTTP_TOO_MANY_REQUESTS
//                    );
//                }
//            }

            $driver = User::updateOrCreate(
                ['phone' => $data['primary_phone']],
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
    public function authLoginConfirm($data)
    {
        try {
            $user = User::where('phone', $data['primary_phone'])->first();
            $codeValid = $user->rand_number == $data['rand_number']
                && $user->phone_confirm_sent
                && $user->phone_confirm_sent->greaterThan(now()->subMinutes(5));

            if ($codeValid && $user) {

                return response()->success([
                    'token' => DB::transaction(function () use ($user, $data) {

                        $user->update([
                            'phone_confirm_at' => now(),
                            'phone_confirm_sent' => null,
                            'hired_at' => now(),
                        ]);


                        $tokenObj = $user->createToken('driver-token');
                        $plainToken = $tokenObj->plainTextToken;
                        $tokenId = $tokenObj->accessToken->id;
                        $d['token'] = $plainToken;
                        $d['role_id'] = $user->role_id;
                        $d['email'] = $user->email;
                        $d['name'] = $user->name;
                        $d['company'] = Company::select('id','company_name','tenet_id')->where('user_id', $user->id)->first();


                        $device = request()->header('User-Agent');
                        UserApiSession::create([
                            'user_id' => $user->id,
                            'device' => $device,
                            'location' => '',
                            'login_at' => now(),
                            'last_active_at' => now(),
                            'token_id' => $tokenId,
                            'ip'=>request()->ip(),
                        ]);
                        return $d;
                    })
                ]);



            }
            return response()->error('Invalid or expired code', 400);
        } catch (\Throwable $e) {
            Log::error('Error: '.$e->getMessage());
            return response()->error(
                $e instanceof QueryException ? $e->getMessage() : 'Internal server error',
                $e instanceof QueryException ? 400 : 500
            );
        }
    }
}
