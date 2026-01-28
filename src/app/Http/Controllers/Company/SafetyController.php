<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeClaimStatusRequest;
use App\Http\Requests\Company\IncidentStatusChangeRequest;
use App\Models\Company\Claim;
use App\Models\Company\ClaimDocument;
use App\Models\Company\DataqChallenge;
use App\Models\Company\Incident;
use App\Models\Company\IncidentFile;
use App\Models\Company\IncidentViolation;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SafetyController extends Controller
{
    public function getInspections(Request $request, $company_id){

        $per_page = $request->per_page ?? 100;
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $driver_id = $request->get('driver_id');
        $truck_id = $request->get('truck_id');
        $status = $request->get('status');
        $search = $request->get('search');
        $state_id = $request->get('state_id');
        $inspection_level_id = $request->get('inspection_level_id');

        $incident = Incident::query()
            ->with(['driver', 'inspection_level', 'state', 'truck'])
            ->withCount('violations')
            ->where('type', 'inspections')
            ->where('company_id', $company_id)

            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('identifier', 'like', '%' . $search . '%')->orWhere('report_number', 'like', '%' . $search . '%');
                });
            })
            ->when($driver_id, function ($query) use ($driver_id) {
                return $query->orderBy('driver_id', $driver_id);
            })
            ->when($truck_id, function ($query) use ($truck_id) {
                return $query->orderBy('truck_id', $truck_id);
            })
            ->when($status, function ($query) use ($status) {
                return $query->orderBy('status', $status);
            })
            ->when($state_id, function ($query) use ($state_id) {
                return $query->orderBy('state_id', $state_id);
            })
            ->when($inspection_level_id, function ($query) use ($inspection_level_id) {
                return $query->orderBy('inspection_level_id', $inspection_level_id);
            })
            ->simplePaginate($per_page);

        return response()->success($incident);
    }
    public function getInspectionDetails($company_id, $incident_id)
    {

        $incident = Incident::with('driver','state','assets','violations')->where('company_id',$company_id)->where('id',$incident_id)->firstorfail();
        return response()->success($incident);
    }
    public function getInspectionCounts($company_id){

        $data['inpection']= Incident::where('type','inspections')->where('company_id',$company_id)->count();
        $data['violations']= Incident::where('type','inspections')->where('status','violations')->where('company_id',$company_id)->count();
        $data['crash']= IncidentViolation::where('company_id',$company_id)->where('violation_category_id',7)->count();
        $response['count'] = $data;

        $dataP['unsafe_driving'] = 33;
        $dataP['controlled_substance'] = 0;
        $dataP['crash_indicator'] = 0;
        $dataP['hos_compliance'] = 33;
        $dataP['driver_fitness'] = 0;
        $dataP['vehicle_maintenance'] = 33;
        $response['percent'] =$dataP;
        return response()->success($response);
    }

    public function getInspectionChallenges($company_id)
    {
        $perPage =request('perPage',100);
        $challenges = DataqChallenge::with(['category', 'driver','inspection'])->where('company_id',$company_id)->simplePaginate($perPage);
        return response()->success($challenges);
    }
    public function getInspectionChallengeDetails($company_id, $challenge)
    {
        $challenges = DataqChallenge::with(['category', 'driver','incident.violations.category','inspection'])->where('company_id',$company_id)->findorfail($challenge);
        return response()->success($challenges);
    }

    public function getIncidents(Request $request, $company_id)
    {
        $per_page = $request->per_page ?? 100;
        $type = $request->type ?? 'accident';
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status');
        $search = $request->get('search');
        $fault = $request->get('fault');

        $allIncidents = ['accident','other_damage','other_incidents'];
        if(!in_array($type,$allIncidents)){
            return response()->error('Invalid type');
        }
        $incident = Incident::query()
            ->with(['driver',  'state'])
            ->withCount('claims')
            ->where('company_id', $company_id)
            ->where('type', $type)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('identifier_formatted', 'like', '%' . $search . '%');
                });
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($fault, function ($query) use ($fault) {
                return $query->where('at_fault', $fault);
            })
            ->simplePaginate($per_page);

        return response()->success($incident);
    }
    public function getIncidentCounts($company_id){
        $incidentStats = Incident::where('company_id', $company_id)
            ->where('type', '!=', 'inspections')
            ->selectRaw("
                COUNT(*) AS total_incidents,
                COUNT(*) FILTER (WHERE status = 'open') AS open,
                COUNT(*) FILTER (WHERE status = 'closed') AS closed
            ")
            ->first();
        $incidentStats_preventable = Incident::where('company_id', $company_id)
            ->where('type', '!=', 'inspections')
            ->where('preventable',1)
            ->count();
        $claimsCount = Claim::where('company_id', $company_id)->where('status','open')->count();
        $totalIncident = $incidentStats->total_incidents ?? 0;
        return response()->success([
            'total_incidents' => (int) $totalIncident,
            'open'            => (int) ($incidentStats->open ?? 0),
            'closed'          => (int) ($incidentStats->closed ?? 0),
            'claims'          => (int) $claimsCount,
            'preventable'     => (int) $incidentStats_preventable,
            'non_preventable' => (int) $totalIncident -$incidentStats_preventable,
        ]);
    }
    public function getIncidentDetails(Request $request, $company_id, $incident_id){
        $type = $request->type;
        $allowedTypes =['details','claims','evidence','timeline'];
        if(!in_array($type,$allowedTypes)){
            return response()->error('Invalid type');
        }
        if($type=='details'){
            $response = Incident::query()
                ->with(['driver',  'state','assets'])
                ->where('company_id', $company_id)->where('id',$incident_id)->firstorfail();
        }elseif ($type=='claims'){
            $response = Claim::where('incident_id',$incident_id)->where('company_id', $company_id)->get();
        }elseif ($type=='evidence'){
            $response= IncidentFile::select('id','type','file_name','file_size')->where('incident_id',$incident_id)->latest()->get();
        }else{
            $response = null;
        }

    }
    public function deleteIncidentDetails($company_id, $incident_id)
    {
        try{
            return DB::transaction(function () use ($incident_id, $company_id) {

                $incident = Incident::with('violations','files')->where('id', $incident_id)->where('company_id',$company_id)->firstorfail();
                foreach ($incident->violations as $violation){
                    $violation->delete();
                }
                foreach ($incident->files as $file){
                    Storage::disk('public')->delete($file->file_path);
                }
                return response()->success($incident,204);
            });
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->error('Something went wrong');
        }
    }
    public function deleteIncidentEvidence($company_id, $incident_id, $evidence_id)
    {
        if(!Incident::where('id',$incident_id)->exists()){
            return response()->error('Incident not found');
        }
        $incidentFiles = IncidentFile::where('incident_id',$incident_id)->findOrFail($evidence_id);
        try{
            return DB::transaction(function () use ($incidentFiles) {
                Storage::disk('public')->delete($incidentFiles->file_path);
                $incidentFiles->delete();
                return response()->success($incidentFiles,204);
            });
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->error('Something went wrong');
        }
    }


    public function getClaims(Request $request, $company_id){
        $per_page = $request->per_page ?? 100;

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status');
        $search = $request->get('search');
        $type = $request->get('type');

        $incident = Claim::with(['driver',  'incident'])
            ->where('company_id', $company_id)

            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('claim_number', 'like', '%' . $search . '%')->orWhere('type', 'like', '%' . $search . '%');
                });
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($type, function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->simplePaginate($per_page);

        return response()->success($incident);
    }
    public function getClaimCounts($company_id){
        $stats = Claim::where('company_id', $company_id)
            ->selectRaw("
                COUNT(*) as total_claims,
                COUNT(*) FILTER (WHERE status = 'open') as open,
                COUNT(*) FILTER (WHERE status = 'closed') as closed,
                COALESCE(SUM(deductible_amount), 0) as total_deductible,
                COALESCE(SUM(insurance_paid), 0) as insurance_paid
    ")
            ->first();

        $stats->recovered_amount = $stats->total_deductible + $stats->insurance_paid;
        return response()->success([
            'total_claims'      => (int) $stats->total_claims,
            'open'              => (int) $stats->open,
            'closed'            => (int) $stats->closed,
            'total_deductible'  => (float) $stats->total_deductible,
            'insurance_paid'    => (float) $stats->insurance_paid,
            'recovered_amount'  => (float) $stats->total_deductible + (float) $stats->insurance_paid,
        ]);
    }
    public function getClaimsDetails(Request $request, $company_id, $claim_id){
        $type = $request->type;
        $allowedTypes =['details','evidence'];
        if(!in_array($type,$allowedTypes)){
            return response()->error('Invalid type');
        }
        if ($type=='evidence'){
            $response = ClaimDocument::select('file_name','file_size')->where('claim_id',$claim_id)->latest()->get();

        }else{
            $response=Claim::with(['driver','incident'])->findOrFail($claim_id);
        }
        return response()->success($response);
    }
    public function claimsChangeStatus(ChangeClaimStatusRequest $request, $company_id, $claim_id){
        $claim = Claim::where('company_id', $company_id)->where('id', $claim_id)->firstorfail();
        try {
            $claim->update($request->validated());
            return response()->success($claim);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->error('Something went wrong');
        }
    }
    public function deleteClaimEvidence($company_id,$claim_id, $evidence_id)
    {
        if(!Claim::where('id',$claim_id)->exists()){
            return response()->error('Claim not found');
        }
        $claimFiles = ClaimDocument::where('claim_id',$claim_id)->findOrFail($evidence_id);
        try{
            return DB::transaction(function () use ($claimFiles) {
                Storage::disk('public')->delete($claimFiles->file_path);
                $claimFiles->delete();
                return response()->success($claimFiles,204);
            });
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->error('Something went wrong');
        }
    }

    public function getCitations(Request $request, $company_id){
        $per_page = $request->per_page ?? 100;

        $search = $request->get('search');

        $incident = Incident::with(['driver',  'violations','truck','agency'])
            ->where('company_id', $company_id)
            ->where('type','citations')

            ->when($search, function ($query) use ($search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('identifier', 'like', '%' . $search . '%');
                });
            })
            ->simplePaginate($per_page);

        return response()->success($incident);
    }
    public function getCitationDetails(Request $request, $company_id, $citation_id){
        $type = $request->type;
        $allowedTypes =['details','evidence'];
        if(!in_array($type,$allowedTypes)){
            return response()->error('Invalid type');
        }
        if ($type=='evidence'){
            $response=IncidentFile::where('incident_id',$citation_id)->get();
        }else{
            $response = Incident::with(['driver','violations','truck','agency'])->where('company_id', $company_id)
                ->where('type','citations')->findOrFail($citation_id);
        }
        return response()->success($response);
    }
    public function citationStatusChange(IncidentStatusChangeRequest $request, $company_id, $citation_id)
    {
        try {
            $incident = Incident::where('company_id', $company_id)->where('id', $citation_id)->firstorfail();
            $incident->update($request->validated());
            return response()->sucess($incident);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->error('Something went wrong');
        }
    }
}
