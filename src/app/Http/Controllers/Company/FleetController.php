<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\VehicleAddRequest;
use App\Http\Requests\Company\VehicleDocumentRequest;
use App\Http\Requests\Company\VehicleInsuranceRequest;
use App\Http\Requests\Company\VehicleMaintenanceRequest;
use App\Http\Requests\Company\VehicleTypeAddRequest;
use App\Models\Driver\Vehicle;
use App\Models\General\VehicleDocumentType;
use App\Models\General\VehicleType;
use App\Services\Company\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FleetController extends Controller
{
    public function __construct(protected VehicleService $service)
    {
    }

    public function getVehicles(Request $request, $company_id){

        $status = $request->status;
        $type = $request->type_id;
        $document = $request->document;
        $search = $request->search;
        $per_page = $request->per_page ?? 100;
        $page = $request->page ?? 1;

        $versionKey = "fleet:{$company_id}:version";
        $version = Cache::get($versionKey, 1);

        $cacheKey = "fleet:{$company_id}:v{$version}:type={$type}:status={$status}:doc={$document}:search={$search}:per={$per_page}:page={$page}";

        $vehicles = Cache::remember($cacheKey, now()->addMinutes(5), function () use (
            $company_id, $type, $status, $document, $search, $per_page
        ) {
            return Vehicle::with(['drivers', 'registration', 'inspection', 'insurance'])
                ->where('company_id', $company_id)
                ->when($type, fn($q) => $q->where('type_id', $type))
                ->when($status, fn($q) => $q->where('status', $status))
                ->when($search, fn($q) => $q->where('unit_number', 'like', "%{$search}%"))
                ->when($document && $document !== 'all', function ($q) use ($document) {
                    $today = now();

                    $q->whereHas('documents', function ($q) use ($document, $today) {

                        if ($document === 'valid') {
                            $q->where('status', 'valid')
                                ->whereDate('expire_at', '>', $today);
                        }

                        if ($document === 'expiring_soon') {
                            $q->whereDate('expire_at', '<=', $today->copy()->addDays(30))
                                ->whereDate('expire_at', '>=', $today);
                        }

                        if ($document === 'expired') {
                            $q->where(function ($q) use ($today) {
                                $q->whereDate('expire_at', '<', $today);
                            });
                        }

                    });
                })
                ->paginate($per_page);
        });

        return response()->success($vehicles);

    }
    public function countVehicles(Request $request, $company_id){
        $today = now();
        $soon = now()->addDays(30);

        // Cache version key
        $versionKey = "fleet:{$company_id}:version";
        $version = Cache::get($versionKey, 1);

        // Cache key includes version + date
        $cacheKey = "fleet:{$company_id}:v{$version}:dashboard";

        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($company_id, $today, $soon) {

            return Vehicle::where('vehicles.company_id', $company_id) // Good practice to prefix here too
            ->selectRaw("
        COUNT(*) AS total_assets,
        -- Use vehicles.status to resolve ambiguity
        COUNT(CASE WHEN vehicles.status = 'active' THEN 1 END) AS active_assets,
        -- Use vehicles.type_id
        COUNT(CASE WHEN vehicles.type_id = '1' THEN 1 END) AS tractors,
        COUNT(CASE WHEN vehicles.type_id = '2' THEN 1 END) AS trailers,
        COUNT(DISTINCT CASE
            WHEN (vd.status = 'expired' OR vd.expires_at < ?)
            THEN vehicles.id
        END) AS expired_documents,
        COUNT(DISTINCT CASE
            WHEN (vd.status = 'valid' AND vd.expires_at BETWEEN ? AND ?)
            THEN vehicles.id
        END) AS expiring_soon
    ", [$today, $today, $soon])
                ->leftJoin('vehicle_documents as vd', 'vd.vehicle_id', '=', 'vehicles.id')
                ->first();
        });

        $percentFleet = $data->total_assets > 0
            ? round(($data->active_assets / $data->total_assets) * 100)
            : 0;

        return response()->json([
            'total_assets' => $data->total_assets,
            'active_assets' => $data->active_assets,
            'expired_documents' => $data->expired_documents,
            'expiring_soon' => $data->expiring_soon,
            'tractors' => $data->tractors,
            'trailers' => $data->trailers,
            'active_percentage' => $percentFleet,
        ]);
    }
    public function vehicleAdd(VehicleAddRequest $request, $company_id)
    {
        $data = $request->validated();
        return $this->service->vehicleAdd($data, $company_id);
    }
    public function documentAdd(VehicleDocumentRequest $request, $company_id,$vehicle_id){
        $data = $request->validated();
        return $this->service->documentAdd($data, $company_id,$vehicle_id);
    }
    public function documentDelete($company_id,$document_id){
        return $this->service->documentDelete($company_id, $document_id);
    }
    public function documentInsuranceAdd(VehicleInsuranceRequest $request, $company_id,$vehicle_id){
        $data = $request->validated();
        return $this->service->documentInsuranceAdd($data, $company_id,$vehicle_id);
    }
    public function documentInsuranceDelete($company_id,$document_id){
        return $this->service->documentInsuranceDelete($company_id, $document_id);
    }



    public function documentMaintenanceAdd(VehicleMaintenanceRequest $request, $company_id,$vehicle_id){
        $data = $request->validated();
        return $this->service->documentMaintenanceAdd($data, $company_id,$vehicle_id);
    }
    public function documentMaintenanceDelete($company_id,$document_id){
        return $this->service->documentMaintenanceDelete($company_id, $document_id);
    }


    public function vehicleAddType(VehicleTypeAddRequest $request, $company_id){
        $data = $request->validated();
        return $this->service->vehicleAddType($data, $company_id);
    }
    public function vehicleTypeDelete($company_id, $vehicleType_id){

        $vehicleCheck = Vehicle::where('type_id', $vehicleType_id)->exists();
        if($vehicleCheck){
            return response()->error('Type already exists on Vehicles, first change Vehicle Type');
        }
        try{
            $deleted = VehicleType::destroy($vehicleType_id);

            if (!$deleted) {
                return response()->error('Vehicle Type not found.', 404);
            }
            return response()->success([],204);
        }catch (\Exception $exception){
            Log::error($exception);
            return response()->error(''.$exception->getMessage());
        }
    }
}
