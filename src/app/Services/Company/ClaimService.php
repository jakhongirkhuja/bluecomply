<?php

namespace App\Services\Company;

use App\Models\Company\Claim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
class ClaimService
{
    public function store($data,$company_id)
    {
        return DB::transaction(function () use ($data, $company_id) {
            $claims = [];

            foreach ($data['claims'] as $index => $claimData) {

                $files = $claimData['files'] ?? [];
                unset($claimData['files']);
                $claimData['company_id'] = $company_id;
                $claimData['incident_id'] = $data['incident_id'];
                $claimData['driver_id'] = $data['driver_id'];
                $claim =Claim::create([
                        'company_id'  => $company_id,
                        'incident_id' => $data['incident_id'],
                        'driver_id'   => $data['driver_id'],
                    ] + $claimData);
                $claim->logActivity('Claim '.$claim->identifier_formatted,'Claim information added','admin',$data['driver_id'],'claims', $claim->id);
                $this->storeFiles($claim, $files);
                $res['type'] = $claim->type;
                $res['identifier-formatted'] = $claim->identifier_formatted;
                $claims[] = $res;
            }
            return $claims;
        });
    }


//    public function update(Claim $claim, array $data): Claim
//    {
//        $claim->update($data);
//        return $claim->load('documents', 'incident');
//    }

    public function delete(Claim $claim): bool
    {
        foreach ($claim->files as $doc) {
            Storage::disk('public')->delete($doc->file_path);
        }

        return $claim->delete();
    }
    protected function storeFiles(Claim $claim, array $files): void
    {
        foreach ($files as $file) {
            $path = $file->storeAs(
                'driver-claims',
                Str::orderedUuid().rand(1,500).'.'.$file->getClientOriginalExtension(),
                'public'
            );

            $claim->files()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }
    }
}
