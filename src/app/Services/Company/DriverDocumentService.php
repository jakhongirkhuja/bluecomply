<?php

namespace App\Services\Company;

use App\Models\Company\Document;
use App\Models\Company\DocumentType;
use App\Models\Company\Restriction;
use App\Models\Driver\Endorsement;
use App\Models\Driver\LicenseDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class DriverDocumentService
{
    public function addFiles($data,$document)
    {
        $this->storeFiles($document, $data['files']);
        return 'File successefully uploaded';
    }
    public function deleteFile($file)
    {
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }
        return $file->delete();
    }
    public function postotherdocs(array $data, array $validate, $document, $company_id)
    {
        return DB::transaction(function () use ($data, $validate, $document, $company_id) {
            $documentType = DocumentType::find($data['document_type_id']);
            $payload = [
                'user_id' => auth()->id(),
                'driver_id' => $data['driver_id'],
                'category_id' =>$data['document_type_id']==7? $validate['category_id'] : $documentType->category_id,
                'document_type_id' => $data['document_type_id'],
                'name' =>$data['document_type_id']==7? $validate['name']  : $documentType->name,
                'number'=> $validate['number']?? null,
                'expires_at' => $validate['expires_at'] ?? null,
                'current'=>false,
                'company_id'=>$company_id,
                'uploaded_by' => auth()->user()?->role === 'admin' ? 'admin' : 'driver',
                'status' => isset($validate['expires_at']) &&
                now()->gt($validate['expires_at']) ? 'expired' : 'valid',
            ];
            if ($document) {
                $document->update($payload);
            } else {
                $document = Document::create($payload);
                $this->storeFiles($document, $validate['files']);
            }
            return $document;
        });
    }
    public function postmaindocs(array $data, array $validate, $document, $company_id)
    {

        return DB::transaction(function () use ($data, $validate, $document, $company_id) {
            $documentType = DocumentType::find($data['document_type_id']);
            $payload = [
                'user_id' => auth()->id(),

                'driver_id' => $data['driver_id'],
                'category_id' =>$documentType->category_id,
                'document_type_id' => $data['document_type_id'],
                'name' => $documentType->name,
                'cdlclasses_id'=>$validate['class_id']?? null,
                'number'=> $validate['number'],
                'issue_at'=> $validate['issue_at'] ?? null,
                'expires_at' => $validate['expires_at'] ?? null,
                'current'=>$validate['current'],
                'company_id'=>$company_id,
                'state_id' => $validate['state_id'],
                'uploaded_by' => auth()->user()?->role === 'admin' ? 'admin' : 'driver',
                'status' => isset($validate['expires_at']) &&
                now()->gt($validate['expires_at']) ? 'expired' : 'valid',
            ];
            if ($document) {
                $document->update($payload);
            } else {
                $document = Document::create($payload);
                if($data['document_type_id']!=4 && $data['document_type_id']!=5){
                    $this->storeFiles($document, $validate['files_front'], 'front');
                    $this->storeFiles($document, $validate['files_back'], 'back');
                }else{
                    $this->storeFiles($document, $validate['files']);
                }
            }
            if(isset($validate['endorsements'])){
                $endorsements = Endorsement::create([
                    'driver_id' => $data['driver_id'],
                    'endorsements' => $validate['endorsements'],
                ]);
            }

            if(isset($validate['restrictions'])){
                foreach ($validate['restrictions'] as $restriction) {
                     Restriction::create([
                        'driver_id' => $data['driver_id'],
                        'restriction_type_id'=>$restriction,
                        'company_id' => $company_id,
                    ]);
                }
            }

            return $document;
        });


    }

    public function delete(Document $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return true;
    }

    public function listByDriver(int $driverId)
    {
        return Document::with('type.category')
            ->where('user_id', $driverId)
            ->get()
            ->groupBy(fn ($doc) => $doc->type->category->name);
    }
    protected function storeFiles(Document $document, array $files, string $side=null): void
    {
        foreach ($files as $file) {
            $path = $file->storeAs(
                'driver-documents',
                Str::orderedUuid().rand(1,500).'.'.$file->getClientOriginalExtension(),
                'public'
            );

            $document->files()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'side' => $side,
            ]);
        }
    }
}
