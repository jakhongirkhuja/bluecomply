<?php

namespace App\Services\Company;

use App\Models\Company\Company;
use App\Models\Driver\EmploymentVerification;
use App\Models\Driver\EmploymentVerificationEvent;
use Illuminate\Support\Facades\DB;

class EmploymentVerificationService
{
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $company = Company::where('user_id', auth()->id())->firstOrFail();
            $employmentPayload =[
                'driver_id' => $data['driver_id'],
                'company_id' => $data['company_id'],
                'direction'=> $data['direction'],
                'method' => $data['method']??null,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'employment_start_date'=>$data['employment_start_date'],
                'employment_end_date'=>$data['employment_end_date'],
                'sent_at'=>now(),
                'created_by_company'=>$company->id,
                'created_by' => auth()->id(),
            ];
            if(isset($data['id'])){
                $employment = EmploymentVerification::findorfail($data['id']);
                $employment->update($employmentPayload);
            }else{
                $employment =  EmploymentVerification::create($employmentPayload);
            }
            $message ='Verification sent';
            $payload =[
                'employment_verification_id'=>$employment->id,
                'type' => 'sent',
                'method' => $data['method'],
                'created_by' => auth()->id(),
            ];
            if(isset($data['method'])){
                if($data['method']=='email'){
                    $message .= ' via email';
                }else{
                    $message = ' via Fax';
                }
            }
            $payload['notes'] = $message;
            EmploymentVerificationEvent::create($payload);
            return $employment;
        });
    }
    public function storeRespond(array $data, $request, $verification){

        return DB::transaction(function () use ($data,$request, $verification) {
            $response = $verification->responses()->create($data);
            if ($request->has('accidents')) {
                foreach ($request->accidents as $accident) {
                    $response->accidents()->create($accident);
                }
            }
            $verification->update(['status'=>$data['status']]);
            return $response;

        });
    }
    public function send(EmploymentVerificationRequest $request): void
    {
        if ($request->status !== 'new') {
            throw new LogicException('Request already sent');
        }

        $request->update([
            'status' => 'pending',
            'sent_at' => now(),
        ]);

        $request->events()->create([
            'type' => 'sent',
            'method' => $request->method,
            'created_by' => auth()->id(),
        ]);
    }

    public function followUp(
        EmploymentVerificationRequest $request,
        string                        $method,
        ?string                       $note = null
    ): void
    {
        if (!in_array($request->status, ['pending'])) {
            throw new LogicException('Cannot follow up this request');
        }

        $request->events()->create([
            'type' => 'follow_up',
            'method' => $method,
            'note' => $note,
            'created_by' => auth()->id(),
        ]);
    }

    public function complete(
        EmploymentVerificationRequest $request,
        string                        $status,
        ?string                       $note = null
    ): void
    {
        if (!in_array($status, ['provided', 'denied', 'completed'])) {
            throw new InvalidArgumentException('Invalid completion status');
        }

        $request->update([
            'status' => $status,
            'completed_at' => now(),
        ]);

        $request->events()->create([
            'type' => 'response',
            'note' => $note,
            'created_by' => auth()->id(),
        ]);
    }

    public function delete(EmploymentVerificationRequest $request): void
    {
        // compliance-safe delete
        $request->delete();
    }
}
