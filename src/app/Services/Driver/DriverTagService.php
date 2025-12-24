<?php

namespace App\Services\Driver;

use App\Models\Driver\DriverTag;

class DriverTagService
{
    public function create(array $data): DriverTag
    {
        $data['user_id'] = auth()->id();
        return DriverTag::create($data);
    }

    public function update(DriverTag $tag, array $data): DriverTag
    {
        $data['user_id'] = auth()->id();
        $tag->update($data);
        return $tag;
    }

    public function delete(DriverTag $tag): bool
    {
        return $tag->delete();
    }
}
