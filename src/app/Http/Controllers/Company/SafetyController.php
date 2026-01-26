<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Claim;
use App\Models\Company\Incident;
use App\Models\Company\IncidentViolation;
use Illuminate\Http\Request;

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
}
