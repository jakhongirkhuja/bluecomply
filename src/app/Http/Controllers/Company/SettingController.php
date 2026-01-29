<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyDerInformationRequets;
use App\Http\Requests\Company\CompanyOwnerEditRequest;
use App\Http\Requests\Company\CompanyPreferenceRequest;
use App\Http\Requests\Company\MvrMonitorStoreRequest;
use App\Http\Requests\Company\NotificationSettingRequest;
use App\Models\Company\Company;
use App\Models\Company\Insurance;
use App\Models\Company\MvrMonitoring;
use App\Models\General\GlobalDocument;
use App\Models\Registration\RegistrationLink;
use App\Models\User;
use App\Services\Company\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function __construct(protected SettingService $service)
    {
    }

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
    public function postUserInformation(CompanyOwnerEditRequest $request, $company_id){
        $user = User::find(Auth::id());
        $company =Company::where('id',$company_id)->where('user_id',$user->id)->first();
        if(!$company){
            return response()->error('Company not found');
        }
        $data = $request->validated();

        return $this->service->postUserInformation($data, $company,$user);
    }
    public function postDerInformation(CompanyDerInformationRequets $request, $company_id){
        return $this->service->postDerInformation($request->validated(), $company_id);
    }
    public function logoutall(Request $request)
    {
        $user = Auth::user();
        $sessions = $user->apiSessions();
        foreach ($sessions as $session) {
            $session->delete();

        }
        $user->tokens()->delete();
        return response()->success($sessions);
    }
    public function deleteAccount(Request $request){
        $user = User::find(Auth::id());
        $user->status ='deleted';
        $user->save();
        $user->tokens()->delete();
        return response()->success($user);
    }
    public function saveNotificationSettings(NotificationSettingRequest $request, $company_id)
    {
        $data = $request->validated();
        return $this->service->saveNotificationSettings($data, $company_id);
    }
    public function mvrGetDrivers(Request $request, $company_id){
        $search = $request->get('search');
        $perPage = $request->get('perPage') ?? 100;
        $allIncidents = ['enrolled','not_enrolled'];
        $type = $request->type ?? 'not_enrolled';
        if(!in_array($type,$allIncidents)){
            return response()->error('Invalid type');
        }

        if($type=='not_enrolled'){
            $type= false;
        }else{
            $type = true;
        }
            $mvrMonitor = MvrMonitoring::with('driver')->where('company_id', $company_id)
                ->where('enrolled', $type)
                ->when($search, function ($query) use ($search) {
                    return $query->whereHas('driver', function ($query) use ($search) {
                        $query->where('first_name', 'like', '%'.$search.'%')->orwhere('last_name', 'like', '%'.$search.'%');
                    });
                })->latest()
                ->simplePaginate($perPage);

        return response()->success($mvrMonitor);
    }
    public function mvrPostDrivers(MvrMonitorStoreRequest $request, $company_id)
    {
        $data = $request->validated();
        return $this->service->mvrPostDrivers($data, $company_id);
    }
    public function generalSettings(CompanyPreferenceRequest $request, $company_id){
        $data = $request->validated();
        return $this->service->generalSettings($data, $company_id);
    }
    public function linkView($company_id)
    {
        $registrationLinks = RegistrationLink::with(['latestView', 'latestStart'])
            ->where('company_id', $company_id)
            ->latest()
            ->simplePaginate(50);
        return response()->success($registrationLinks);
    }
}
