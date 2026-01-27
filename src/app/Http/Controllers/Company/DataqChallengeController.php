<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\DataqChallengeRequest;
use App\Models\Company\DataqChallenge;
use App\Services\Company\DataqChallengeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DataqChallengeController extends Controller
{
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
    public function __construct(protected DataqChallengeService $service)
    {
    }

    public function index($company_id)
    {
        $perPage =request('perPage',100);
        $challenges = DataqChallenge::with(['category', 'driver','inspection'])->where('company_id',$company_id)->simplePaginate($perPage);
        return response()->success($challenges);
    }
    public function show($company_id, $challenge)
    {
        $challenges = DataqChallenge::with(['category', 'driver','incident.violations.category','inspection'])->where('company_id',$company_id)->findorfail($challenge);
        return response()->success($challenges);
    }
    public function store(DataqChallengeRequest $request, $company_id)
    {
        $data = $request->validated();
        return $this->safe(fn() => response()->success($this->service->store($data, $company_id,$request),Response::HTTP_CREATED));
    }

    public function destroy(DataqChallenge $dataq_challenge)
    {
        $dataq_challenge->delete();
        return response()->success($dataq_challenge,204);
    }
}
