<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\ApplicationStatusTypeRequest;
use App\Http\Requests\Driver\ApplicationTypeRequest;
use App\Http\Requests\Driver\DriverLoginConfirmRequest;
use App\Http\Requests\Driver\DriverLoginRequest;
use App\Http\Requests\Driver\DriverTypeCheckRequest;
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
    public function applicationStatus(ApplicationStatusTypeRequest $request)
    {
        $type = $request->validated()['type'];
        return $this->applicationService->applicationStatus($type);
    }
    public function applicationDetails(ApplicationTypeRequest $request)
    {
        $type = $request->validated()['type'];
        return $this->applicationService->getTypeDetails($type);
    }
    public function applicationDetailPost(DriverTypeCheckRequest $request)
    {
        return $this->applicationService->postTypeDetails($request->validated(),$request);
    }

    public function driverLogin(DriverLoginRequest $request){
        return $this->applicationService->driverLogin($request->validated());
    }
    public function driverLoginConfirm(DriverLoginConfirmRequest $request){
        return $this->applicationService->driverLoginConfirm($request->validated());
    }
    public function driverLogout(Request $request){
        $request->user('driver')->currentAccessToken()->delete();
        return response()->success('Logged out successfully', Response::HTTP_OK);
    }

}
