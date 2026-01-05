<?php

namespace App\Services\Company;

use App\Models\Company\Claim;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
class ClaimService
{
    public function store(array $data): Claim
    {
        $claim = Claim::create($data);

        if (!empty($data['documents'])) {
            foreach ($data['documents'] as $file) {
                $this->storeFiles($file, $data['files']);

            }
        }
        return $claim->load('documents', 'incident');
    }


    public function update(Claim $claim, array $data): Claim
    {
        $claim->update($data);
        return $claim->load('documents', 'incident');
    }

    public function delete(Claim $claim): bool
    {
        foreach ($claim->documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
        }

        return $claim->delete();
    }
    protected function storeFiles(Claim $claim, array $files, string $side=null): void
    {
        foreach ($files as $file) {
            $path = $file->storeAs(
                'driver-claims',
                Str::orderedUuid().rand(1,500).'.'.$file->getClientOriginalExtension(),
                'public'
            );

            $claim->documents()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'side' => $side,
            ]);
        }
    }
}
