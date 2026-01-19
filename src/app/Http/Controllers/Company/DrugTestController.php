<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\DrugTestRequest;
use App\Models\Driver\DrugTest;
use App\Services\Driver\DrugTestService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DrugTestController extends Controller
{
    public function __construct(protected DrugTestService $service)
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

    public function store(DrugTestRequest $request, $comapny_id)
    {
        return $this->safe(fn() => response()->success($this->service->create($request->validated()), 201));
    }

    public function update(DrugTestRequest $request, $comapny_id, DrugTest $drugTest)
    {
        return $this->safe(fn() => response()->success($this->service->update($drugTest, $request->validated()), 201));
    }

    public function destroy($comapny_id, DrugTest $drugTest)
    {
        return $this->safe(fn() => response()->success($this->service->delete($drugTest), 201));

    }
}
