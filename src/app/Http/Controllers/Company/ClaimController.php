<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreClaimRequest;
use App\Models\Company\Claim;
use App\Models\Driver\Driver;
use App\Services\Company\ClaimService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
class ClaimController extends Controller
{
    public function __construct(protected ClaimService $service)
    {
    }

    // Show a single claim
    public function show($id)
    {
        return response()->success(
            Claim::with('documents', 'incident', 'incident.driver')
                ->where('id', $id)
                ->firstOrFail()
        );
    }

    // Store a new claim
    public function store(StoreClaimRequest $request)
    {
        $data = $request->validated();

        $driverExist = Driver::where('id', $data['driver_id'])
            ->whereHas('company', fn($q) => $q->where('user_id', auth()->id()))
            ->firstOrFail();

        $data['company_id'] = $driverExist->company_id;

        return $this->safe(fn() =>
        response()->success(
            $this->service->store($data),
            Response::HTTP_CREATED
        )
        );
    }

    // Update an existing claim
    public function update(StoreClaimRequest $request, Claim $claim)
    {
        $data = $request->validated();

        return $this->safe(fn() =>
        response()->success(
            $this->service->update($claim, $data),
            Response::HTTP_OK
        )
        );
    }

    // Delete a claim
    public function destroy(Claim $claim)
    {
        return $this->safe(fn() =>
        response()->success(
            $this->service->delete($claim),
            Response::HTTP_OK
        )
        );
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
}
