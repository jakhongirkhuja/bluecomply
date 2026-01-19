<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\DriverTerminationRequest;
use App\Models\Driver\Termination;
use App\Services\Driver\DriverTerminationService;
use Symfony\Component\HttpFoundation\Response;

class DriverTerminationController extends Controller
{
    public function __construct(protected DriverTerminationService $service)
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
    public function store(DriverTerminationRequest $request, $company_id)
    {
        return $this->safe(fn() => response()->success($this->service->create($request->validated()),Response::HTTP_CREATED));
    }
    public function destroy(Termination $driverTermination, $company_id)
    {
        return $this->safe(fn() => response()->success($this->service->delete($driverTermination)));
    }
}
