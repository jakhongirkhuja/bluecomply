<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\RegistrationLinkRequest;
use App\Models\Driver\Driver;
use App\Models\Registration\RegistrationLink;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LinkGeneratorController extends Controller
{
    public function index()
    {
        return response()->success(
            RegistrationLink::where('user_id', Auth::id())->latest()->paginate(30)
        );
    }
    public function store(RegistrationLinkRequest $request, $company_id)
    {
        $data = $request->validated();
        $data['company_id'] = $company_id;
        if(isset($data['driver_id'])){
            $driver = Driver::find($data['driver_id']);
            $driver->driver_temp_token = (string) Str::orderedUuid();
            $driver->save();
            $data['driver_token'] = (string)$driver->driver_temp_token;
            $data['purpose'] = $data['purpose'];
        }
        $data['user_id'] = Auth::id();
        $regLink = RegistrationLink::create($data);
        $link['link'] = $regLink->link;
        return response()->success($link, Response::HTTP_CREATED);
    }
    public function show(RegistrationLink $registrationLink, $company_id)
    {
        return response()->success($registrationLink);
    }
    public function update( RegistrationLinkRequest $request, RegistrationLink $registrationLink, $company_id) {
        $data = $request->validated();
        if(isset($data['driver_id'])){
            $driver = Driver::find('id', $data['driver_id']);
            $driver->driver_temp_token = Str::orderedUuid();
            $data['driver_token'] = $driver->driver_token;
        }
        $registrationLink->update($data);
        return response()->success($registrationLink);
    }
    public function destroy(RegistrationLink $registrationLink, $company_id)
    {
        $registrationLink->delete();
        return response()->success(null,Response::HTTP_NO_CONTENT);
    }
}
