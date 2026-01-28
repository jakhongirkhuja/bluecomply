<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\AssignToAssetRequet;
use App\Http\Requests\Company\UploadDocumentRequest;
use App\Models\Company\Incident;
use App\Models\Company\Insurance;
use App\Models\General\GlobalDocument;
use App\Services\Company\DocumentService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $service)
    {
    }

    public function getDocuments(Request $request, $company_id)
    {
        return $this->service->getDocuments($request, $company_id);
    }
    public function deleteDocuments(Request $request, $company_id, $id){
        $allIncidents = ['document','insurance'];
        $type = $request->type ?? 'document';
        if(!in_array($type,$allIncidents)){
            return response()->error('Invalid type');
        }
        if($request->type=='document'){
            return $this->service->deleteDocument( $company_id,$id);
        }else{
            return $this->service->deleteInsurance($company_id,$id);
        }

    }
    public function uploadDocument(UploadDocumentRequest $request, $company_id){
        $data = $request->validated();
        $allIncidents = ['insurance','company'];
        $type = $request->type ?? 'insurance';
        if(!in_array($type,$allIncidents)){
            return response()->error('Invalid type');
        }
        if($type=='insurance'){
            return $this->service->uploadDocument($data,$company_id);
        }else{
            return response()->error('Document not exist for now',404);
        }

    }
    public function assignToAsset(AssignToAssetRequet $request,$comapny_id){
        $data = $request->validated();
        return $this->service->assignToAsset($data,$comapny_id);
    }
}
