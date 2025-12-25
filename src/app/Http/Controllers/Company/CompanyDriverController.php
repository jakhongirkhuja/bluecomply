<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\DriverProfileChangeRequest;
use App\Http\Requests\Company\DriverStatusChangeRequest;
use App\Http\Requests\Company\GetStatusRequest;
use App\Http\Requests\Driver\DriverTypeCheckRequest;
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

        $drivers = Driver::with(['license', 'med', 'drugTest','truck'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate();
        return response()->success($drivers, Response::HTTP_OK);
    }
    public function addDriver(DriverTypeCheckRequest $request)
    {
        return $this->safe(fn() => response()->success($this->service->addDriver($request->validated(), $request),Response::HTTP_CREATED));
    }
    public function drivers_change_status(DriverStatusChangeRequest $request){
        return $this->safe(fn() => response()->success($this->service->drivers_change_status($request->validated()),Response::HTTP_CREATED));
    }
    public function drivers_change_profile(DriverProfileChangeRequest $request){
        return $this->safe(fn() => response()->success($this->service->drivers_change_profile($request->validated()),Response::HTTP_CREATED));

    }
}
