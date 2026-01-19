<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\DriverTagRequest;
use App\Models\Driver\DriverTag;
use App\Services\Driver\DriverTagService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DriverTagController extends Controller
{
    public function __construct(protected DriverTagService $service)
    {
    }

    protected function safe(callable $callback)
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            report($e);
            return response()->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }
    public function store(DriverTagRequest $request, $comapny_id)
    {
        return $this->safe(fn() => response()->success($this->service->create($request->validated(),$comapny_id), Response::HTTP_CREATED));
    }
    public function update(DriverTagRequest $request, DriverTag $driverTag,$comapny_id)
    {
        return $this->safe(fn() => response()->success($this->service->update($driverTag, $request->validated(),$comapny_id), Response::HTTP_CREATED));
    }
    public function destroy(DriverTag $driverTag, $comapny_id)
    {
        return $this->safe(fn() => response()->success($this->service->delete($driverTag, $comapny_id), Response::HTTP_CREATED));
    }
}
