<?php

namespace App\Services\Company;

use App\Models\General\GlobalDocument;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
class GlobalDocumentService
{
    public function sync(Model $source,array $data = [] ): GlobalDocument {
        return GlobalDocument::updateOrCreate(
            [
                'source_table' => $source->getTable(),
                'source_id'    => $source->id,
            ],
            $this->mapData($source, $data)
        );
    }

    protected function mapData(Model $source, array $data): array
    {
        return [
            // system / derived
            'expiration'  => $data['expiration'] ?? null,
            'upload_date' => $data['upload_date'] ?? now()->toDateString(),
            'status'      => $this->resolveStatus($data['expiration'] ?? null),

            'name'        => $data['name']        ?? null,
            'category'    => $data['category']    ?? null,
            'type'        => $data['type']        ?? null,
            'related_to'  => $data['related_to']  ?? null,

            'uploaded_by_id'          => $data['uploaded_by_id'] ?? null,
            'uploaded_by_table_name'  => $data['uploaded_by_table_name'] ?? null,
        ];
    }

    protected function resolveStatus(?string $expiration): string
    {
        if (!$expiration) {
            return 'Valid';
        }

        $date = Carbon::parse($expiration);

        if ($date->isPast()) {
            return 'Expired';
        }

        if ($date->diffInDays(now()) <= 30) {
            return 'Expiring Soon';
        }

        return 'Valid';
    }

    public function delete(Model $source): void
    {
        GlobalDocument::where([
            'source_table' => $source->getTable(),
            'source_id' => $source->id,
        ])->delete();
    }
}
