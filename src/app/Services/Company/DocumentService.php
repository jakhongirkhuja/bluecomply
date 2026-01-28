<?php

namespace App\Services\Company;

use App\Models\Company\Document;
use App\Models\Company\Insurance;
use App\Models\Company\VehicleInsurance;
use App\Models\General\GlobalDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function getDocuments($request, $company_id){
        $per_page = $request->per_page ?? 100;
        $type = $request->type ?? 'document';
        $search = $request->get('search');
        $category = $request->get('category');
        $allIncidents = ['document','insurance'];
        if(!in_array($type,$allIncidents)){
            return response()->error('Invalid type');
        }
        if($type=='document'){
            $documents = GlobalDocument::where('company_id', $company_id)

                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
                })
                ->when($category, function ($query) use ($category) {
                    return $query->where('category', $category);
                })
                ->latest()
                ->simplePaginate($per_page);
        }else{
            $documents = Insurance::with('insuranceType')->where('company_id', $company_id)
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($query) use ($search) {
                        $query->where('related_to', 'like', '%' . $search . '%');
                    });
                })->latest()
                ->simplePaginate($per_page);
        }
        return response()->success($documents);
    }
    public function deleteInsurance( $company_id,$id){
        $insurance = Insurance::where('id', $id)->where('company_id',$company_id)->first();
        $vehicleInsurance = VehicleInsurance::where('company_id', $company_id)->where('insurance_id', $insurance->id)->exists();
        if($vehicleInsurance){
            return response()->error('Vehicle insurance cannot be deleted, first change vehicle insurance first');
        }
        $insurance->delete();
        return response()->success($insurance,204);
    }
    public function deleteDocument( $company_id,$id)
    {
        $globalDocument = GlobalDocument::where('id', $id)->where('company_id',$company_id)->firstorfail();
        $document = Document::with('files')->where('id', $globalDocument->source_id)->where('company_id',$company_id)->first();
        if($document){
            foreach($document->files as $file){
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }
            $document->delete();

        }
        $globalDocument->delete();
        return response()->success($document,204);
    }
    public function uploadDocument($data,$company_id)
    {
        try {
            $insurance = Insurance::create([
                'company_id' => $company_id,
                'vehicle_insurance_type_id' => $data['document_type_id'],
                'company_type_id' => $data['company_type_id'],
                'related_to' => $data['related_to'],
                'expires_at'=>$data['expires_at'],
            ]);
            return response()->success($insurance);
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return response()->error('Something went wrong');
        }
    }
    public function assignToAsset($data,$company_id)
    {
        try {
            $vehicleIds = array_unique($data['vehicles']);
            $insuranceIds = array_unique($data['insurances']);
            return DB::transaction(function () use ($company_id, $vehicleIds, $insuranceIds) {

                $now = Carbon::now();

                DB::table('vehicle_insurances')
                    ->where('company_id', $company_id)
                    ->whereIn('vehicle_id', $vehicleIds)
                    ->where('current', true)
                    ->update([
                        'current'    => false,
                        'updated_at' => $now,
                    ]);


                DB::table('vehicle_insurances')
                    ->where('company_id', $company_id)
                    ->whereIn('vehicle_id', $vehicleIds)
                    ->whereIn('vehicle_insurance_type_id', $insuranceIds)
                    ->update([
                        'current'    => true,
                        'updated_at' => $now,
                    ]);

                $existing = DB::table('vehicle_insurances')
                    ->where('company_id', $company_id)
                    ->whereIn('vehicle_id', $vehicleIds)
                    ->whereIn('vehicle_insurance_type_id', $insuranceIds)
                    ->get(['vehicle_id', 'vehicle_insurance_type_id'])
                    ->map(fn($row) => $row->vehicle_id . '_' . $row->vehicle_insurance_type_id)
                    ->toArray();

                $toInsert = [];
                foreach ($vehicleIds as $vId) {
                    foreach ($insuranceIds as $iId) {
                        $key = $vId . '_' . $iId;
                        if (!in_array($key, $existing)) {
                            $toInsert[] = [
                                'company_id'                => $company_id,
                                'vehicle_id'                => $vId,
                                'vehicle_insurance_type_id' => $iId,
                                'current'                   => true,
                                'created_at'                => $now,
                                'updated_at'                => $now,
                            ];
                        }
                    }
                }

                if (!empty($toInsert)) {
                    DB::table('vehicle_insurances')->insert($toInsert);
                }

                return response()->success(true);
            });

        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return response()->error('Something went wrong');
        }
    }
}
