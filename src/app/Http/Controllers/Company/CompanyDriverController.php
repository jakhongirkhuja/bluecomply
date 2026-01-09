<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\DriverProfileChangeRequest;
use App\Http\Requests\Company\DriverReviewRequest;
use App\Http\Requests\Company\DriverStatusChangeRequest;
use App\Http\Requests\Company\GetDriverDetailRequest;
use App\Http\Requests\Company\GetStatusRequest;
use App\Http\Requests\Company\StoreTaskRequest;
use App\Http\Requests\Driver\DriverTypeCheckRequest;
use App\Models\Company\Company;
use App\Models\Company\Task;
use App\Models\Driver\Driver;
use App\Services\Company\DriverService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyDriverController extends Controller
{
    public function __construct(protected DriverService $service)
    {
    }
    protected function safe(callable $callback)
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            report($e);
            return response()->error(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
    public function getDrivers(GetStatusRequest $request){

        $data = $request->validated();
        $company_id = Company::where('id',$data['company_id'])->where('user_id',auth()->id())->value('id');
        $drivers = Driver::with(['license', 'med', 'drugTest','truck'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })->where('company_id', $company_id)
            ->latest()
            ->paginate();
        return response()->success($drivers, Response::HTTP_OK);
    }
    public function getDriverDetails(GetDriverDetailRequest $request, $driver_id){

        $driverDetails = $this->service->getDriverDetails($driver_id, $request->validated());
        return response()->success($driverDetails, Response::HTTP_OK);
    }
    public function getDriverIncidentAnalytics( $driver_id)
    {
        Driver::where('id', $driver_id)->whereHas('company', fn($q) => $q->where('user_id', auth()->id()))->firstOrFail();

        $driverDetails = $this->service->getDriverIncidentAnalytics($driver_id);
        return response()->success($driverDetails, Response::HTTP_OK);
    }
    public function addDriver(DriverTypeCheckRequest $request)
    {
        return $this->safe(fn() => response()->success($this->service->addDriver($request->validated(), $request),Response::HTTP_CREATED));
    }
    public function addTask(StoreTaskRequest $request)
    {
        return $this->safe(fn() => response()->success($this->service->addTask($request),Response::HTTP_CREATED));

    }
    public function drivers_change_status(DriverStatusChangeRequest $request){
        return $this->safe(fn() => response()->success($this->service->drivers_change_status($request->validated()),Response::HTTP_CREATED));
    }
    public function drivers_change_profile(DriverProfileChangeRequest $request){
        return $this->safe(fn() => response()->success($this->service->drivers_change_profile($request->validated()),Response::HTTP_CREATED));

    }
    public function drivers_review(DriverReviewRequest  $request, $id)
    {
        $driver =Driver::findorfail($id);
        if($driver->status !='new'){
            return response()->error('Driver current status is not new',Response::HTTP_NOT_FOUND);
        }
        return $this->safe(fn() => response()->success($this->service->drivers_review($request->validated(), $driver),Response::HTTP_CREATED));

    }
}
