<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\DriverLoginConfirmRequest;
use App\Http\Requests\Driver\DriverLoginRequest;
use App\Models\Driver\Application;
use App\Models\Driver\LicenseDetail;
use App\Models\Driver\MedDetail;
use App\Services\Driver\ApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
class ApplicationController extends Controller
{
    public function __construct(protected ApplicationService $applicationService)
    {
    }
    public function applicationStatus()
    {
        return $this->applicationService->applicationStatus();
    }
    public function applicationDetails(Request $request)
    {
        return $this->applicationService->getTypeDetails($request->type);
    }
    public function applicationDetailPost(Request $request)
    {
        return $this->applicationService->postTypeDetails($request);
    }

    public function driverLogin(DriverLoginRequest $request){
        return $this->applicationService->driverLogin($request->validated());
    }
    public function driverLoginConfirm(DriverLoginConfirmRequest $request){
        return $this->applicationService->driverLoginConfirm($request->validated());
    }
    public function driverLogout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->success('Logged out successfully', Response::HTTP_OK);
    }

}
