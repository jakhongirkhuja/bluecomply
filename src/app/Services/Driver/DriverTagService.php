<?php

namespace App\Services\Driver;

use App\Models\Driver\DriverTag;

class DriverTagService
{
    public function create(array $data, $comapny_id)
    {
        $driverTag = [];
        foreach ($data['tag'] as $tag) {
            $driverTag[]= DriverTag::create([
                'driver_id' => $data['driver_id'],
                'tag'       => $tag,
                'user_id'   => auth()->id()
            ]);
        }
        return $driverTag;
    }

    public function update(DriverTag $tag, array $data, $comapny_id): DriverTag
    {
        $data['user_id'] = auth()->id();
        $tag->update($data);
        return $tag;
    }

    public function delete(DriverTag $tag, $comapny_id): bool
    {
        return $tag->delete();
    }
}
