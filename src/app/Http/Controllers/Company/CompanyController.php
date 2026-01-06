<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Models\Company\Company;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index()
    {
        return response()->success(Company::with('managers','plan','user')->where('user_id', Auth::id())->latest()->paginate());
    }
    public function store(CompanyRequest $request)
    {
        $company = Company::create($request->validated());

        return response()->success($company, Response::HTTP_CREATED);
    }
    public function show(Company $company)
    {
        return response()->success($company);
    }
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $company->update($request->validated());

        return response()->success($company);
    }
    public function destroy(Company $company)
    {
        $company->delete();

        return response()->success(null, Response::HTTP_NO_CONTENT);
    }
}
