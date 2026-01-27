<?php

namespace App\Services\Company;

use App\Models\Company\DataqChallenge;
use App\Models\Company\Document;
use App\Models\Company\Task;
use App\Models\Driver\Driver;
use Illuminate\Support\Str;

class DataqChallengeService
{

    public function store($data,$company_id, $request)
    {
        $data['company_id'] = $company_id;
        $data['status'] = 'pending';
        $challenge = DataqChallenge::create($data);

        if ($request->hasFile('files')) {

            $this->storeFiles($challenge,$request->file('files'), $company_id);
        }
        return $challenge;
    }
    protected function storeFiles(DataqChallenge $document, array $files,$company_id): void
    {
        foreach ($files as $file) {
            $path = $file->storeAs(
                'company-challenges',
                Str::orderedUuid() . rand(1, 500) . '.' . $file->getClientOriginalExtension(),
                'public'
            );

            $document->files()->create([
                'company_id' => $company_id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }
    }
}
