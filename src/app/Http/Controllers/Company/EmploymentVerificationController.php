<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreEmploymentVerificationRequest;
use App\Models\Driver\EmploymentVerification;
use App\Services\Company\EmploymentVerificationService;
use Illuminate\Http\Response;

class EmploymentVerificationController extends Controller
{
    public function __construct(protected EmploymentVerificationService $service)
    {
    }

    public function show($id)
    {
        return response()->success(EmploymentVerification::with('company.user')
            ->where('id', $id)
            ->whereHas('company', fn($q) => $q->where('user_id', auth()->id()) )
            ->firstOrFail());
    }

    public function store(StoreEmploymentVerificationRequest $request)
    {
        return $this->safe(fn() => response()->success($this->service->store($request->validated()), Response::HTTP_CREATED));
    }

    // Create Verification

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
//
//    // Send Verification
//    public function send(EmploymentVerification $verification) {
//        $this->service->send($verification);
//        return response()->json(['success'=>true]);
//    }
//
//    // Follow-up
//    public function followUp(Request $request, EmploymentVerification $verification) {
//        $this->service->followUp($verification, $request->method, $request->note);
//        return response()->json(['success'=>true]);
//    }
//
//    // Provide Response
//    public function respond(StoreEmploymentVerificationResponseRequest $request, EmploymentVerification $verification) {
//        $response = $verification->responses()->create($request->validated());
//
//        if ($request->has('accidents')) {
//            foreach ($request->accidents as $accident) {
//                $response->accidents()->create($accident);
//            }
//        }
//
//        return new EmploymentVerificationResponseResource($response->load('accidents'));
//    }
//
//    // Complete Verification
//    public function complete(Request $request, EmploymentVerification $verification) {
//        $this->service->complete($verification, $request->status);
//        return response()->json(['success'=>true]);
//    }
//
//    // Delete Verification
//    public function destroy(EmploymentVerification $verification) {
//        $this->service->delete($verification);
//        return response()->json(['success'=>true]);
//    }
}
