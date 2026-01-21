<?php

namespace App\Services\Company;

use App\Models\Company\Incident;
use App\Models\Company\VehicleDocument;
use App\Models\Company\VehicleDocumentFile;
use App\Models\Company\VehicleInsurance;
use App\Models\Company\VehicleInsuranceFile;
use App\Models\Company\VehicleMaintenance;
use App\Models\Company\VehicleMaintenanceFile;
use App\Models\Driver\Vehicle;
use App\Models\General\VehicleType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VehicleService
{
    public function vehicleAdd($validated, $company_id){
        $payload = [
            'company_id'    => $company_id,
            'type_id'       => $validated['type_id'],
            'unit_number'   => $validated['number'],
            'status'        => $validated['status'],
            'make'          => $validated['make'],
            'model'         => $validated['model'],
            'year'          => $validated['year'],
            'vin'           => $validated['vin'],
            'plate'         => $validated['plate'],
            'state_id'      => $validated['state_id'],
        ];

        try{
            return DB::transaction(function () use ($payload, $company_id, $validated) {

                if (isset($validated['id'])) {
                    $vehicle = Vehicle::find($validated['id']);
                    if (!$vehicle) {
                        return response()->error('Vehicle not found', 404);
                    }

                    $vehicle->update($payload);
                } else {

                    $vehicle = Vehicle::create($payload);

                    $documentsToCreate = [
                        [
                            'type_id' => 1,
                            'file'    => $validated['files_registration'],
                            'expiry'  => $validated['expire_at']
                        ],
                        [
                            'type_id' => 4,
                            'file'    => $validated['files_inspection'],
                            'expiry'  => $validated['inspection_at']
                        ]
                    ];
                    foreach ($documentsToCreate as $doc) {
                        $newDoc = VehicleDocument::create([
                            'vehicle_id'               => $vehicle->id,
                            'vehicle_document_type_id' => $doc['type_id'],
                            'company_id'               => $company_id,
                            'expires_at'                => $doc['expiry'],
                            'status'                   => 'active',
                            'current'                  => true,
                        ]);
                        $this->storeFiles($newDoc, $doc['file'], $company_id, $vehicle->id, $doc['type_id']);
                    }
                }
                return response()->success($vehicle->fresh());
            });
        }catch (\Exception $exception){
            Log::error($exception);
            return response()->error(''.$exception->getMessage());
        }
    }

    public function vehicleAddType($data, $company_id){

        try{
            $vehicleType = VehicleType::create([
                'company_id'=>$company_id,
                'name'=>$data['name'],
            ]);
            return response()->success($vehicleType);
        }catch (\Exception $exception){
            Log::error($exception);
            return response()->error(''.$exception->getMessage());
        }
    }

    public function documentAdd($validated, $company_id, $vehicle_id){
        try{
            return DB::transaction(function () use ($validated, $company_id, $vehicle_id) {

                $documentId = $validated['id'] ?? null;
                $newDoc = VehicleDocument::updateOrCreate([
                    'id' => $documentId, // Если ID есть, Laravel найдет запись, если нет — создаст
                ],[
                    'vehicle_id'               =>$vehicle_id,
                    'vehicle_document_type_id' => $validated['type_id'],
                    'company_id'               => $company_id,
                    'description'               =>$validated['description'],
                    'expires_at'                => $validated['expires_at'],
                    'status'                   => 'active',
                    'current'                  => true,
                ]);
                if (!empty($validated['files'])) {
                    $this->storeFiles($newDoc, $validated['files'], $company_id, $vehicle_id, $validated['type_id']);
                }


                VehicleDocument::where('vehicle_id', $vehicle_id)->where('vehicle_document_type_id',$validated['type_id'])->where('company_id',$company_id)->where('id','!=',$newDoc->id)->update(['current'=>false]);
                return response()->success($newDoc);
            });

        }catch (\Exception $exception){
            Log::error($exception);
            return response()->error(''.$exception->getMessage());
        }
    }
    public function documentDelete($company_id, $document_id)
    {
        try{

            $files = VehicleDocumentFile::where('vehicle_document_id', $document_id)->where('company_id', $company_id)->get();

            foreach ($files as $file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }
            VehicleDocument::destroy($document_id);
            return response()->success([],204);
        }catch (\Exception $exception){
            Log::error($exception);
            return response()->error(''.$exception->getMessage());
        }
    }

    public function documentInsuranceAdd($validated, $company_id, $vehicle_id){
        try{
            return DB::transaction(function () use ($validated, $company_id, $vehicle_id) {

                $documentId = $validated['id'] ?? null;
                $newDoc = VehicleInsurance::updateOrCreate([
                    'id' => $documentId,
                ],[
                    'vehicle_id'               =>$vehicle_id,
                    'vehicle_insurance_type_id' => $validated['type_id'],
                    'company_id'               => $company_id,
                    'description'               =>$validated['description'],
                    'expires_at'                => $validated['expires_at'],
                    'status'                   => 'active',
                    'current'                  => true,
                ]);
                if (!empty($validated['files'])) {
                    $this->storeFiles($newDoc, $validated['files'], $company_id, $vehicle_id, $validated['type_id'],'insurance');
                }

                VehicleInsurance::where('vehicle_id', $vehicle_id)->where('vehicle_insurance_type_id', $validated['type_id'])->where('company_id',$company_id)->where('id','!=',$newDoc->id)->update(['current'=>false]);
                return response()->success($newDoc);
            });

        }catch (\Exception $exception){
            Log::error($exception);
            return response()->error(''.$exception->getMessage());
        }
    }
    public function documentInsuranceDelete($company_id, $document_id)
    {
        try{

            $files = VehicleInsuranceFile::where('vehicle_insurance_id', $document_id)->where('company_id', $company_id)->get();

            foreach ($files as $file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }
            VehicleInsurance::destroy($document_id);
            return response()->success([],204);
        }catch (\Exception $exception){
            Log::error($exception);
            return response()->error(''.$exception->getMessage());
        }
    }





    public function documentMaintenanceAdd($validated, $company_id, $vehicle_id){
        try{
            return DB::transaction(function () use ($validated, $company_id, $vehicle_id) {

                $documentId = $validated['id'] ?? null;
                $newDoc = VehicleMaintenance::updateOrCreate([
                    'id' => $documentId,
                ],[
                    'vehicle_id'               =>$vehicle_id,
                    'vehicle_maintenance_type_id' => $validated['type_id'],
                    'company_id'               => $company_id,
            'service_date'=>            $validated['service_date'],
                    'mileage'               =>$validated['mileage'],
                    'description'               =>$validated['description'],
                    'vendor_name'               =>$validated['vendor_name'],
                    'next_due_type'               =>$validated['next_due_type'],
                    'next_due_date'               =>$validated['next_due_date'],

                    'status'                   => 'active',
                    'current'                  => true,
                ]);
                if (!empty($validated['files'])) {
                    $this->storeFiles($newDoc, $validated['files'], $company_id, $vehicle_id, $validated['type_id'],'maintenance');
                }

                VehicleMaintenance::where('vehicle_id', $vehicle_id)->where('vehicle_maintenance_type_id', $validated['type_id'])->where('company_id',$company_id)->where('id','!=',$newDoc->id)->update(['current'=>false]);
                return response()->success($newDoc);
            });

        }catch (\Exception $exception){
            Log::error($exception);
            return response()->error(''.$exception->getMessage());
        }
    }
    public function documentMaintenanceDelete($company_id, $document_id)
    {
        try{
            $files = VehicleMaintenanceFile::where('vehicle_maintenance_id', $document_id)->where('company_id', $company_id)->get();

            foreach ($files as $file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }
            VehicleMaintenance::destroy($document_id);
            return response()->success([],204);
        }catch (\Exception $exception){
            Log::error($exception);
            return response()->error(''.$exception->getMessage());
        }
    }

    protected function storeFiles($vehicleDocument, $files,$company_id,$vehicle_id,$type, $documentCategory=null): void
    {
        foreach ($files as $file) {
            $path = $file->storeAs(
                'company-vehicles-documents',
                Str::orderedUuid().rand(1,500).'.'.$file->getClientOriginalExtension(),
                'public'
            );
            $payload = [
                'vehicle_id'=>$vehicle_id,
                'company_id'=>$company_id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ];
            if($documentCategory=='insurance'){
                $payload['vehicle_insurance_type_id'] = $type;
            }elseif($documentCategory=='maintenance'){
                $payload['vehicle_maintenance_type_id'] = $type;
            }else{
                $payload['vehicle_document_type_id'] = $type;
            }

            $vehicleDocument->files()->create($payload);
        }


    }
}
