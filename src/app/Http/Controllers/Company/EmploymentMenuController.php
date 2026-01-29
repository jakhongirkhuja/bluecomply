<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Insurance;
use App\Models\Driver\EmploymentPeriod;
use App\Models\Driver\EmploymentVerification;
use App\Models\General\GlobalDocument;
use Illuminate\Http\Request;

class EmploymentMenuController extends Controller
{
    public function getEmployments(Request $request, $company_id){
        $per_page = $request->per_page ?? 100;
        $type = $request->type ?? 'outgoing';
        $search = $request->get('search');
        $status = $request->get('status');
        $order = $request->get('order_by') ?? 'a-z';
        $allIncidents = ['outgoing','incoming'];
        if(!in_array($type,$allIncidents)){
            return response()->error('Invalid type');
        }
        if($type=='outgoing'){
            $response = EmploymentVerification::with('events', 'responses', 'company')
                ->where('created_by_company', $company_id)->latest()

//                ->when($search, function ($query) use ($search) {
//                    return $query->where(function ($query) use ($search) {
//                        $query->where('name', 'like', '%' . $search . '%');
//                    });
//                })
                ->when($status, function ($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->latest()
                ->simplePaginate($per_page);
        }else{
            $response = EmploymentVerification::with('events', 'responses', 'companyby.user','driver')
                ->where('company_id', $company_id)
                ->latest()
                ->simplePaginate($per_page);
        }
        return response()->success($response);
    }
}
