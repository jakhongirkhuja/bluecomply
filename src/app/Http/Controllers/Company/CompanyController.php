<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AddUserToCompany;
use App\Http\Requests\Admin\EditUserInCompany;
use App\Http\Requests\Company\CompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Models\Company\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
    public function addUser(AddUserToCompany $request, $company){
        $data = $request->validated();
        $user =User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role_id' => 2,
        ]);
        $company = Company::findorfail($company);
        $company->users()->attach($user->id);
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

        return response()->success($user);
    }
    public function deleteUser($company, $user)
    {
        $company = Company::findOrFail($company);
        $user = User::findOrFail($user);
        $company->users()->detach($user->id);
        $user->delete();
        return response()->success('User removed from company successfully', Response::HTTP_NO_CONTENT);
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
