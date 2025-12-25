<?php

namespace App\Services\Driver;

use App\Models\Driver\Driver;
use App\Models\Driver\Termination;

class DriverTerminationService
{
    public function create(array $data)
    {
        $driver = Driver::find($data['driver_id']);
        $termination = Termination::updateOrCreate(
            ['driver_id' => $data['driver_id']],
            $data
        );
        $driver->update([
            'status' => 'terminated',
        ]);

        return $termination;
    }
    public function delete(Termination $termination)
    {
        $driver = Driver::find($termination->driver_id);
        $driver->update([
            'status' => 'active',
        ]);
        return $termination->delete();
    }


}
