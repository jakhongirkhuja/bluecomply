<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\DriverProfileChangeRequest;
use App\Http\Requests\Company\DriverReviewRequest;
use App\Http\Requests\Company\DriverStatusChangeRequest;
use App\Http\Requests\Company\GetDriverDetailRequest;
use App\Http\Requests\Company\GetStatusRequest;
use App\Http\Requests\Company\SaveFilterRequest;
use App\Http\Requests\Company\StoreTaskRequest;
use App\Http\Requests\Company\UpdateFilterNameRequest;
use App\Http\Requests\Driver\DriverTypeCheckRequest;
use App\Models\Company\Company;
use App\Models\Company\SavedFilter;
use App\Models\Company\Task;
use App\Models\Driver\Driver;
use App\Services\Company\DriverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CompanyDriverController extends Controller
{
    public function __construct(protected DriverService $service)
    {
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

    public function countDrivers($company_id){
        $now  = now();
        $soon = now()->addDays(30);
        $cacheKey = "drivers.counts.company_id:{$company_id}";
        $counts = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($company_id, $now, $soon) {
            return Driver::where('company_id', $company_id)
                ->selectRaw("
                    COUNT(*) as all,
                    COUNT(*) FILTER (WHERE status = 'do_not_dispatch') as do_not_dispatch,
                    COUNT(*) FILTER (WHERE EXISTS (
                        SELECT 1 FROM documents
                        WHERE documents.driver_id = drivers.id
                          AND documents.expires_at < ?
                    )) as expired_documents,
                    COUNT(*) FILTER (WHERE EXISTS (
                        SELECT 1 FROM documents
                        WHERE documents.driver_id = drivers.id
                          AND documents.expires_at BETWEEN ? AND ?
                    )) as expiring_documents,
                    COUNT(*) FILTER (WHERE EXISTS (
                        SELECT 1 FROM documents
                        WHERE documents.driver_id = drivers.id
                          AND NOT EXISTS (
                              SELECT 1 FROM document_files
                              WHERE document_files.document_id = documents.id
                          )
                    )) as missing_documents
        ", [$now, $now, $soon])
                ->first()->toArray();
        });
        return response()->success($counts);
    }
    public function saveFilterList($company_id){
        return response()->success(SavedFilter::where('company_id', $company_id)->get());
    }
    public function saveFilter(SaveFilterRequest $request, $company_id){

        return $this->safe(fn() => response()->success($this->service->saveFilter($request->validated(), $company_id),Response::HTTP_CREATED));

    }
    public function saveFilterDelete($company_id, $filter_id)
    {
        return response()->success(SavedFilter::where('id',$filter_id)->delete(), 204);
    }
    public function updateFilterName(UpdateFilterNameRequest $request, $company_id, $id){


        return $this->safe(fn() => response()->success(SavedFilter::where('company_id', $company_id)->where('id', $id)->update($request->validated()),Response::HTTP_CREATED));

    }
    public function getDrivers(GetStatusRequest $request,$company_id){

        $filter = $request->filter ?? 'all';
        $category = $request->category;
        $search = $request->search;
        $cacheKey = "drivers.company:{$company_id}";
        $per_page = $request->per_page ?? 100;
        if($request->per_page || $request->search ){
            Cache::forget($cacheKey);
        }

        $drivers = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request, $company_id,$filter, $per_page, $search, $category) {

            $now  = now();
            $soon = now()->addDays(30);

            return Driver::with(['license', 'med', 'drugTest', 'truck'])
                ->where('company_id', $company_id)

                ->when($filter == 'do_not_dispatch', function ($query) {
                    $query->where('status','do_not_dispatch');
                })
                ->when($category, function ($query) use ($category) {
                    $query->where('status',$category);
                })
                ->when($filter == 'expired_documents', function ($query) use ($now) {
                    $query->whereExists(function ($sub) use ($now) {
                        $sub->selectRaw(1)
                            ->from('documents')
                            ->whereColumn('documents.driver_id', 'drivers.id')
                            ->where('documents.expires_at', '<', $now);
                    });
                })

                ->when($filter == 'expiring_documents', function ($query) use ($now, $soon) {
                    $query->whereExists(function ($sub) use ($now, $soon) {
                        $sub->selectRaw(1)
                            ->from('documents')
                            ->whereColumn('documents.driver_id', 'drivers.id')
                            ->whereBetween('documents.expires_at', [$now, $soon]);
                    });
                })

                ->when($filter == 'missing_documents', function ($query) {
                    $query->whereExists(function ($sub) {
                        $sub->selectRaw(1)
                            ->from('documents')
                            ->whereColumn('documents.driver_id', 'drivers.id')
                            ->whereNotExists(function ($sub2) {
                                $sub2->selectRaw(1)
                                    ->from('document_files')
                                    ->whereColumn('document_files.document_id', 'documents.id');
                            });
                    });
                })
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('primary_phone', 'ILIKE', "%{$search}%")
                            ->orWhere('first_name', 'ILIKE', "%{$search}%")
                            ->orWhere('middle_name', 'ILIKE', "%{$search}%")
                            ->orWhere('last_name', 'ILIKE', "%{$search}%");
                    });
                })

                ->latest()
                ->simplePaginate($per_page);
        });

        return response()->success($drivers);
    }
    public function getDriverDetails(GetDriverDetailRequest $request, $company_id,$driver_id){

        $driverDetails = $this->service->getDriverDetails($driver_id, $request->validated(),$company_id);
        return response()->success($driverDetails, Response::HTTP_OK);
    }
    public function getDriverIncidentAnalytics($company_id, $driver_id)
    {
        Driver::where('id', $driver_id)->where('company_id',$company_id)->firstOrFail();

        $driverDetails = $this->service->getDriverIncidentAnalytics($driver_id, $company_id);
        return response()->success($driverDetails, Response::HTTP_OK);
    }
    public function addDriver(DriverTypeCheckRequest $request, $company_id)
    {
        return $this->safe(fn() => response()->success($this->service->addDriver($request->validated(), $request, $company_id),Response::HTTP_CREATED));
    }
    public function addTask(StoreTaskRequest $request, $company_id)
    {
        return $this->safe(fn() => response()->success($this->service->addTask($request, $company_id),Response::HTTP_CREATED));

    }
    public function drivers_change_status(DriverStatusChangeRequest $request){
        return $this->safe(fn() => response()->success($this->service->drivers_change_status($request->validated()),Response::HTTP_CREATED));
    }
    public function drivers_change_profile(DriverProfileChangeRequest $request){
        return $this->safe(fn() => response()->success($this->service->drivers_change_profile($request->validated()),Response::HTTP_CREATED));

    }
    public function drivers_review(DriverReviewRequest  $request,$company_id,$id)
    {
        $driver =Driver::where('company_id',$company_id)->findorfail($id);
        if($driver->status !='new'){
            return response()->error('Driver current status is not new',Response::HTTP_NOT_FOUND);
        }
        return $this->safe(fn() => response()->success($this->service->drivers_review($request->validated(), $driver, $company_id),Response::HTTP_CREATED));

    }
}
