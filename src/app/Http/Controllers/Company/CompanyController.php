<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AddUserToCompany;
use App\Http\Requests\Admin\EditUserInCompany;
use App\Http\Requests\Company\CompanyFeatures;
use App\Http\Requests\Company\CompanyFileRequest;
use App\Http\Requests\Company\CompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Models\Admin\Plan;
use App\Models\Company\Company;
use App\Models\Company\CompanyFile;
use App\Models\Company\DrugTestOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'status' => 'nullable|string|in:active,suspended,trial',
        ]);
        $query = Company::with(['managers','plan','user']);

        if ($from = request('time_from')) {
            $from = Carbon::parse($from)->toDateString(); // "YYYY-MM-DD"
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = request('time_to')) {
            $to = Carbon::parse($to)->toDateString();
            $query->whereDate('created_at', '<=', $to);
        }

        if ($planId = request('plan_id')) {
            $query->where('plan_id', $planId);
        }
        if ($status = request('status')) {
            $query->where('status', $status);
        }
        if ($search = request('search')) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(tenet_id) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(dot_number) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(company_name) LIKE ?', ["%{$search}%"]);
            });
        }

        // Cache key based on filters & page
        $cacheKey = 'companies_page_' . request('page', 1)
            . '_from_' . ($from ?? 'null')
            . '_to_' . ($to ?? 'null')
            . '_plan_' . ($planId ?? 'null')
            . '_status_' . ($status ?? 'null')
            . '_search_' . ($search ?? 'null');

//        $companies = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($query) {
//            return $query->latest()->paginate(20); // adjust per page
//        });

        $companies = $query->latest()->paginate(20);

        return response()->success($companies);
    }
    public function store(CompanyRequest $request)
    {
        $data = $request->validated();
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' =>$data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make('password123'),
                'role_id' => 2
            ]);
            $data['user_id'] = $user->id;
            unset($data['name']);
            unset($data['email']);
            unset($data['phone']);
            if($data['status']=='trial'){
                $data['drivers'] = 20;
                $data['plan_id'] = null;
            }elseif($data['status']=='active'){
                $plan = Plan::findorfail($data['plan_id']);
                $data['drivers'] = $plan->driver_limit;
            }else{
                $data['plan_id'] = null;
            }
            if(isset($data['file'])){
                $file = $data['file'];
                $path = $file->storeAs(
                    'company-logo',
                    Str::orderedUuid().rand(1,500).'.'.$file->getClientOriginalExtension(),
                    'public'
                );
                $data['logo'] = $path;
            }

            $company = Company::create($data);

            return response()->success($company, Response::HTTP_CREATED);
        });

    }

    public function show(Request $request, $company)
    {

        $company = Company::with('user')
            ->withCount(['drivers as active_drivers' => function($q) {
                $q->where('status', 'active');
            }])
            ->findOrFail($company);

        $data = ['company' => $company];

        if ($metrics = request('type')=='metrics') {

            $cacheKey = "company_metrics_{$company->id}";
            $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($company) {

                return [
                    'active_drivers' => $company->active_drivers,
                    'dq' => 0,
                    'revenue' => 0,
                    'clearinghouse' => 0,
                    'tests' => DrugTestOrder::where('company_id', $company->id)->count(),
                    'tickets' => 0,
                ];
            });
        }
        if ($users = request('type')=='users') {

            $cacheKey = "company_users_{$company->id}";

            $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($company) {
                return [
                    'users' => $company->users()->with('role')
                        ->get(),
                ];
            });
        }
        if ($users = request('type')=='forms') {

            $cacheKey = "company_forms_{$company->id}";

            $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($company) {
                return [
                    'files' => $company->files() ->get(),
                ];
            });
        }

        return response()->success($data);

    }
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $data = $request->validated();
        if($data['status']=='trial'){
            $data['drivers'] = 20;
            $data['plans'] = null;
        }elseif($data['status']=='active'){
            $plan = Plan::findorfail($data['plan_id']);
            $data['drivers'] = $plan->driver_limit;
        }else{
            $data['plans'] = null;
        }
        $company->update($data);

        return response()->success($company);
    }
    public function destroy(Company $company)
    {
        $company->update(['status'=>'suspended']);
        return response()->success($company, Response::HTTP_OK);
    }
    public function addFeatures(CompanyFeatures $request, $company){
        $company = Company::findorfail($company);
        $company->update($request->validated());

        return response()->success($company, Response::HTTP_OK);
    }
    public function addFiles(CompanyFileRequest $request, $company){
        $company = Company::findorfail($company);
        $data = $request->validated();

        foreach ($data['files'] as $file) {
            $path = $file->storeAs(
                'company-documents',
                Str::orderedUuid() . rand(1, 500) . '.' . $file->getClientOriginalExtension(),
                'public'
            );
            $company->files()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }
        $cacheKey = "company_forms_{$company->id}";
        Cache::forget($cacheKey);
        return response()->success($company, Response::HTTP_OK);
    }
    public function deleteFiles($company,$id)
    {

        $file = CompanyFile::where('company_id', $company)->findorfail($id);
        if($file){

            try{

                Storage::disk('public')->delete($file->file_path);
                $file->delete();
                $cacheKey = "company_forms_{$company}";
                Cache::forget($cacheKey);
                return response()->success($company, Response::HTTP_NO_CONTENT);

            }catch (\Exception $e){
                Log::error($e->getMessage());
                return response()->error('Error deleting file');
            }

        }

    }
    public function addUser(AddUserToCompany $request, $company){
        $data = $request->validated();
        $user =User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
        ]);
        $company = Company::findorfail($company);
        $company->users()->attach($user->id);
        $cacheKey = "company_users_{$company->id}";
        Cache::forget($cacheKey);
        $cacheKey = "company_metrics_{$company->id}";
        Cache::forget($cacheKey);
        return response()->success($user, Response::HTTP_CREATED);
    }
    public function editUser(EditUserInCompany $request, $company, $id)
    {
        $data = $request->validated();
        $user = User::findOrFail($id);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);

        $company = Company::findOrFail($company);
        if (!$company->users()->where('user_id', $user->id)->exists()) {
            $company->users()->attach($user->id);
        }
        $cacheKey = "company_users_{$company->id}";
        Cache::forget($cacheKey);
        return response()->success($user);
    }
    public function deleteUser($company, $user)
    {
        $company = Company::findOrFail($company);
        $user = User::findOrFail($user);
        $company->users()->detach($user->id);
        $user->delete();
        $cacheKey = "company_users_{$company->id}";
        Cache::forget($cacheKey);
        $cacheKey = "company_metrics_{$company->id}";
        Cache::forget($cacheKey);
        return response()->success('User removed from company successfully', Response::HTTP_NO_CONTENT);
    }
}
