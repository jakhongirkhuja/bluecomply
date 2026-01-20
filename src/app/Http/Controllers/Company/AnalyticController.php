<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\AnalyticParamsRequest;
use App\Http\Requests\Company\StoreEmploymentVerificationRequest;
use App\Http\Requests\Company\StoreEmploymentVerificationResponseRequest;
use App\Models\Company\Claim;
use App\Models\Company\Company;
use App\Models\Company\Document;
use App\Models\Company\DrugTestOrder;
use App\Models\Company\Incident;
use App\Models\Company\RandomPoolMembership;
use App\Models\Company\RandomSelection;
use App\Models\Company\Task;
use App\Models\Driver\Driver;
use App\Models\Driver\EmployerInformation;
use App\Models\Driver\EmploymentVerification;
use App\Models\Driver\EmploymentVerificationResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticController extends Controller
{
    public function getAnalytics(AnalyticParamsRequest $request, $company_id){
        $data = $request->validated();
        return match ($data['type']) {
            'counts' => $this->getCount($request,$company_id),
            'compliance' => $this->getCompliance($request, $company_id),
            'tasks' => $this->getTasks($request,$company_id),
            'testMonitor' => $this->getTestMonitor($request, $company_id),
            'employment' => $this->getEmployment($request, $company_id),
            'road' => $this->getRoad($request, $company_id),
            'employeeCount' => $this->getEmployeeCount($request, $company_id),
            default => throw new \InvalidArgumentException('Invalid application type'),
        };

    }
    private function getCount($request,$companyId){
        $now = now();
        $currentStart = $now->copy()->subMonth();
        $previousStart = $now->copy()->subMonths(2);

        $data = Cache::remember("company:$companyId:driver_stats", 600, function () use ($companyId, $now, $currentStart, $previousStart) {
            $current = Driver::where('company_id', $companyId)
                ->whereBetween('created_at', [$currentStart, $now])
                ->count();
            $previous = Driver::where('company_id', $companyId)
                ->whereBetween('created_at', [$previousStart, $currentStart])
                ->count();
            $total = Driver::where('company_id', $companyId)->count();



            $currentInspection = Incident::where('company_id', $companyId)
                ->where('type', 'inspections')
                ->whereBetween('created_at', [$currentStart, $now])
                ->count();

            $previousInspection = Incident::where('company_id', $companyId)
                ->where('type', 'inspections')
                ->whereBetween('created_at', [$previousStart, $currentStart])
                ->count();

            $totalInspection = Incident::where('company_id', $companyId)
                ->where('type', 'inspections')
                ->count();


            $currentDrugTests = DrugTestOrder::where('company_id', $companyId)
                ->whereBetween('created_at', [$currentStart, $now])
                ->count();

            $previousDrugTests = DrugTestOrder::where('company_id', $companyId)
                ->whereBetween('created_at', [$previousStart, $currentStart])
                ->count();

            $totalDrugTests = DrugTestOrder::where('company_id', $companyId)->count();


            $currentEVRequests = EmploymentVerification::where('company_id', $companyId)
                ->whereBetween('created_at', [$currentStart, $now])
                ->count();

            $previousEVRequests = EmploymentVerification::where('company_id', $companyId)
                ->whereBetween('created_at', [$previousStart, $currentStart])
                ->count();

            $totalEVRequests = EmploymentVerification::where('company_id', $companyId)
                ->count();
            return response()->success([
                'drivers' => [
                    'total' => $total,
                    'current' => $current,
                    'previous' => $previous,
                    'percentage' => $previous > 0
                        ? round((($current - $previous) / $previous) * 100, 2)
                        : 100,
                ],
                'inspections' => [
                    'total' => $totalInspection,
                    'current' => $currentInspection,
                    'previous' => $previousInspection,
                    'percentage' => $previousInspection > 0
                        ? round((($currentInspection - $previousInspection) / $previousInspection) * 100, 2)
                        : 100,
                ],
                'drugTests' => [
                    'total' => $totalDrugTests,
                    'current' => $currentDrugTests,
                    'previous' => $previousDrugTests,
                    'percentage' => $previousDrugTests > 0
                        ? round((($currentDrugTests - $previousDrugTests) / $previousDrugTests) * 100, 2)
                        : 100,
                ],
                'evRequests' => [
                    'total' => $totalEVRequests,
                    'current' => $currentEVRequests,
                    'previous' => $previousEVRequests,
                    'percentage' => $previousEVRequests > 0
                        ? round((($currentEVRequests - $previousEVRequests) / $previousEVRequests) * 100, 2)
                        : 100,
                ],
            ]);
        });

        return $data;
    }
    private function getCompliance($request, $companyId){
        $now = now();
        $soon = now()->copy()->addDays(30);
        $data = Cache::remember("company:$companyId:driver_documents", now()->addMinutes(10), function () use ($companyId, $now, $soon) {
            // 1) EXPIRED
            $expired = Document::where('company_id', $companyId)
                ->where('expires_at', '<', $now)
                ->selectRaw("COUNT(*) as documents, COUNT(DISTINCT driver_id) as drivers")
                ->first();

            // 2) EXPIRING SOON
            $expiring = Document::where('company_id', $companyId)
                ->whereBetween('expires_at', [$now, $soon])
                ->selectRaw("COUNT(*) as documents, COUNT(DISTINCT driver_id) as drivers")
                ->first();

            // 3) MISSING (documents without files)
            $missing = Document::where('company_id', $companyId)
                ->doesntHave('files')
                ->selectRaw("COUNT(*) as documents, COUNT(DISTINCT driver_id) as drivers")
                ->first();

            // 4) PENDING REVIEW
            $pending = Document::where('company_id', $companyId)
                ->where('status', 'pending')
                ->selectRaw("COUNT(*) as documents, COUNT(DISTINCT driver_id) as drivers")
                ->first();

            return response()->success([
                'expired' => [
                    'documents' => $expired->documents,
                    'drivers' => $expired->drivers,
                ],
                'expiring_soon' => [
                    'documents' => $expiring->documents,
                    'drivers' => $expiring->drivers,
                ],
                'missing' => [
                    'documents' => $missing->documents,
                    'drivers' => $missing->drivers,
                ],
                'pending_review' => [
                    'documents' => $pending->documents,
                    'drivers' => $pending->drivers,
                ],
            ]);


        });
        return $data;

    }
    private function getTasks($request, $companyId){
        $cacheKey = "company:{$companyId}:tasks";

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request, $companyId) {

            $now = Carbon::now();

            $query = Task::where('company_id', $companyId);
            if ($request->has('priority')) {
                $query->where('priority', $request->priority);
            }
            $tasks = $query->orderBy('due_date', 'asc')->get();
            return [
                'tasks' => $tasks
            ];
        });

        return response()->success($data);
    }
    private function getTestMonitor($request, $companyId){
        $cacheKey = "company:{$companyId}:random_pool_test_monitor";
        $type = request()->category;
        $result = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($type, $companyId) {
            if($type=='random_pool'){
                return RandomPoolMembership::where('company_id', $companyId)->latest()->limit(7)->get();
            }
            if($type=='random_test'){
                return DrugTestOrder::where('company_id', $companyId)->latest()->limit(7)->get();
            }

            return [];
        });
        return response()->success($result);
    }
    private function getEmployment($request, $companyId)
    {
        $cacheKey = "company:{$companyId}:employment_request";
        $type = request()->category;


        $result = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($type, $companyId) {
            if($type=='sent'){
                return   EmploymentVerification::with('events', 'responses', 'company')->where('created_by_company', $companyId)->latest()->limit(7)->get();

            }
            if($type=='received'){

                return   EmploymentVerification::with('events', 'responses', 'company')->where('company_id', $companyId)->latest()->limit(7)->get();
            }
            return [];
        });
        return response()->success($result);
    }
    private function getRoad($request, $companyId){
        $cacheKey = "company:{$companyId}:incidents_by_type";
        $type = request()->category;
        $result = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($type, $companyId) {
            $result = Incident::where('company_id', $companyId)
                ->when($type, function ($query) use ($type) {
                    $query->where('type', $type);
                })
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get();
            return $result;
        });

        return response()->success($result);
    }
    private function getEmployeeCount($request, $companyId){
        $filter = $request->filter ?? 'month';
        $cacheKey = "company:{$companyId}:employee_count:{$filter}";

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($filter, $companyId) {

            $now = Carbon::now();
            $query = Driver::where('company_id', $companyId);

            if ($filter == 'day') {
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();

                $data = $query->whereBetween('created_at', [$start, $end])
                    ->select(
                        DB::raw("TO_CHAR(created_at, '%H') AS period"),
                        DB::raw("COUNT(*) AS count")
                    )
                    ->groupBy('period')
                    ->orderBy('period')
                    ->pluck('count', 'period')
                    ->toArray();

                $result = [];
                for ($i = 0; $i <= 23; $i++) {
                    $result['hour_'.$i] = $data[$i] ?? 0;
                }
                return $result;
            }

            if ($filter == 'week') {
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();

                $data = $query->whereBetween('created_at', [$start, $end])
                    ->select(
                        DB::raw("TO_CHAR(created_at, '%w') AS period"),
                        DB::raw("COUNT(*) AS count")
                    )
                    ->groupBy('period')
                    ->orderBy('period')
                    ->pluck('count', 'period')
                    ->toArray();

                $result = [];
                for ($i = 0; $i <= 6; $i++) {
                    $result['day_'.$i] = $data[$i] ?? 0;
                }
                return $result;
            }

            if ($filter == 'month') {
                $start = $now->copy()->subMonth();
                $end = $now;

                $data = Driver::where('company_id', $companyId)
                    ->whereBetween('created_at', [$start, $end])
                    ->select(
                        DB::raw("TO_CHAR(created_at, 'YYYY-MM-DD') AS period"),
                        DB::raw("COUNT(*) AS count")
                    )
                    ->groupBy('period')
                    ->orderBy('period')
                    ->pluck('count', 'period')
                    ->toArray();

                $result = [
                    'week_1' => 0,
                    'week_2' => 0,
                    'week_3' => 0,
                    'week_4' => 0,
                    'week_5' => 0,
                ];

                foreach ($data as $day => $count) {
                    $week = Carbon::parse($day)->weekOfMonth;
                    $result['week_'.$week] += $count;
                }

                return $result;
            }
            if($filter == 'year'){

                $start = $now->copy()->subMonths(12);
                $end = $now;

                $data = $query->whereBetween('created_at', [$start, $end])
                    ->select(
                        DB::raw("TO_CHAR(created_at, '%m') AS period"),
                        DB::raw("COUNT(*) AS count")
                    )
                    ->groupBy('period')
                    ->orderBy('period')
                    ->pluck('count', 'period')
                    ->toArray();

                $result = [];
                for ($i = 1; $i <= 12; $i++) {
                    $result['month_'.$i] = $data[str_pad($i, 2, '0', STR_PAD_LEFT)] ?? 0;
                }
                return $result;
            }

            return [];
        });
        return response()->success($data);
    }

    public function compliance($companyId){
        $now = now();
        $soon = now()->copy()->addDays(30);
        $page = request()->get('page', 1);
        $search = request()->get('search');
        $cacheKey = "documents:company:$companyId:page:$page:"
            . "status:" . request()->status
            . ":type:" . request()->type
            . ":sort:" . request()->sort
            . ":day_range:" . request()->day_range
            . ":search:" . $search;

        $documents = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($companyId, $now, $soon, $search) {

            $query = Document::query()
                ->select('documents.*')->where('documents.company_id', $companyId)
                ->join('drivers', 'drivers.id', '=', 'documents.driver_id')
                ->join('document_types', 'document_types.id', '=', 'documents.document_type_id');


            if (request()->status == 'missing') {
                $query->whereNotExists(function ($sub) {
                    $sub->selectRaw(1)
                        ->from('document_files')
                        ->whereColumn('document_files.document_id', 'documents.id');
                });
            }

            if (request()->status == 'expired') {
                $query->where('expires_at', '<', $now);
            }

            if (request()->status == 'expired_soon') {
                $query->whereBetween('expires_at', [$now, $soon]);
            }

            if (request()->status == 'pending') {
                $query->where('status', 'pending');
            }

            if (request()->type) {
                $query->where('document_type_id', request()->type);
            }

            if (request()->day_range == '0-7') {
                $query->whereBetween('expires_at', [$now, $now->copy()->addDays(7)]);
            }
            if (request()->day_range == '8-30') {
                $query->whereBetween('expires_at', [$now->copy()->addDays(8), $now->copy()->addDays(30)]);
            }
            if (request()->day_range == '31-60') {
                $query->whereBetween('expires_at', [$now->copy()->addDays(31), $now->copy()->addDays(60)]);
            }
            if (request()->day_range == '90') {
                $query->where('expires_at', '>=', $now->copy()->addDays(90));
            }
            if (request()->day_range == 'expired') {
                $query->where('expires_at', '<', $now);
            }

            $query->where(function ($q) use ($now, $soon) {
                $q->where('expires_at', '<', $now)
                    ->orWhereBetween('expires_at', [$now, $soon])
                    ->orWhereNotExists(function ($sub) {
                        $sub->selectRaw(1)
                            ->from('document_files')
                            ->whereColumn('document_files.document_id', 'documents.id');
                    });
            });
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('drivers.first_name', 'LIKE', "%{$search}%")
                        ->orWhere('document_types.name', 'LIKE', "%{$search}%")
                        ->orWhere('documents.name', 'LIKE', "%{$search}%")
                        ->orWhere('documents.number', 'LIKE', "%{$search}%");
                });
            }
            if (request()->sort == 'expires_at_overdue') {
                $query->orderByRaw("expires_at < ? DESC", [$now]);
            }

            if (request()->sort == 'driver_name') {
                $query->orderBy('drivers.first_name');
            }

            if (request()->sort == 'type') {
                $query->orderBy('document_type_id');
            }

            if (request()->sort == 'expires_at') {
                $query->orderBy('expires_at');
            }

            return $query->simplePaginate(20);
        });

        return response()->success($documents);
    }
}
