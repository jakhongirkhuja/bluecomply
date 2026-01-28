<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyOwnerEditRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function getUserInformation(){
        $user = Auth::user();
        $sessions = $user->apiSessions()
            ->latest('login_at')
            ->get()
            ->map(function($session) {
                return [
                    'device' => $session->device,
                    'location' => $session->location,
                    'login_at' => $session->login_at,
                    'last_active_at' => $session->last_active_at,
                ];
            });
        return response()->json([
            'user' => $user,
            'sessions' => $sessions,
        ]);
    }
    public function postUserInformation(CompanyOwnerEditRequest $request){

    }
    public function logoutall()
    {
        $user = Auth::user();
        $sessions = $user->apiSessions();
        foreach ($sessions as $session) {
            $session->delete();
            Auth::logout();
        }
        return response()->success($sessions);
    }
    public function deleteAccount(){

    }

}
