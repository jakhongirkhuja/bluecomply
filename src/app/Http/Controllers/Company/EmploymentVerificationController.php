<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreEmploymentVerificationRequest;
use App\Http\Requests\Company\StoreEmploymentVerificationResponseRequest;
use App\Models\Driver\EmploymentVerification;
use App\Services\Company\EmploymentVerificationService;
use Illuminate\Http\Request;

class EmploymentVerificationController extends Controller
{
    protected $service;
    public function __construct(EmploymentVerificationService $service) {
        $this->service = $service;
    }

    // Create Verification
    public function store(StoreEmploymentVerificationRequest $request) {
        $verification = $this->service->store($request->validated());
        return response()->json($verification, 200);
    }

    // Send Verification
    public function send(EmploymentVerification $verification) {
        $this->service->send($verification);
        return response()->json(['success'=>true]);
    }

    // Follow-up
    public function followUp(Request $request, EmploymentVerification $verification) {
        $this->service->followUp($verification, $request->method, $request->note);
        return response()->json(['success'=>true]);
    }

    // Provide Response
    public function respond(StoreEmploymentVerificationResponseRequest $request, EmploymentVerification $verification) {
        $response = $verification->responses()->create($request->validated());

        if ($request->has('accidents')) {
            foreach ($request->accidents as $accident) {
                $response->accidents()->create($accident);
            }
        }

        return new EmploymentVerificationResponseResource($response->load('accidents'));
    }

    // Complete Verification
    public function complete(Request $request, EmploymentVerification $verification) {
        $this->service->complete($verification, $request->status);
        return response()->json(['success'=>true]);
    }

    // Delete Verification
    public function destroy(EmploymentVerification $verification) {
        $this->service->delete($verification);
        return response()->json(['success'=>true]);
    }
}
