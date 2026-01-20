<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProfileController extends Controller
{
    public function profileCompanies()
    {
        $data = Cache::remember('profile.companies', now()->addMinutes(5), function () {
            return Company::select('id','logo','company_name','tenet_id')->where('user_id', Auth::id())->get();
        });
        return response()->success($data);
    }
    public function profileLogout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully, token revoked.'
        ], 200);
    }
}
