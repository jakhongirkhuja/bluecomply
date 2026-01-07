<?php

namespace App\Services\Company;

use App\Models\Company\Document;
use App\Models\Company\Incident;
use App\Models\Driver\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IncidentService
{
    public function store(array $data): Incident
    {

        return DB::transaction(function () use ($data) {
            $incident = Incident::updateOrCreate(
                ['id' => $data['id'] ?? null],
                $data
            );

            if (($data['truck'] ?? null) === 'manual') {
                $truck = Vehicle::updateOrCreate(
                    ['id' => $incident->truck_id ?? null],
                    [
                        'type' => 'Truck',
                        'unit_number' => $data['truck_unit_number'],
                        'make' => $data['truck_make'],
                        'vin' => $data['truck_vin'],
                        'plate' => $data['truck_plate'],
                        'plate_state' => $data['truck_plate_state_id'],
                    ]
                );
                $incident->truck_id = $truck->id;
                $incident->save();
                $this->assignVehicleToDriver($incident->driver_id, $truck->id, 'Truck');
            }
            if (($data['trailer'] ?? null) === 'manual') {

                $trailer = Vehicle::updateOrCreate(
                    ['id' =>  $incident->trailer_id ?? null],
                    [
                        'type' => 'Trailer',
                        'unit_number' => $data['trailer_unit_number'],
                        'make' => $data['trailer_make'],
                        'vin' => $data['trailer_vin'],
                        'plate' => $data['trailer_plate'],
                        'plate_state' => $data['trailer_plate_state_id'],
                    ]
                );
                $incident->trailer_id = $trailer->id;
                $incident->save();
                $this->assignVehicleToDriver($incident->driver_id, $trailer->id, 'Trailer');
            }


            return $incident;
        });

    }

    public function create(array $data, $incident): Incident
    {
        return $incident->update($data);
    }

    public function update(Incident $incident, array $data): Incident
    {
        $incident->update($data);
        return $incident;
    }

    public function delete(Incident $incident): void
    {
        $incident->delete();
    }

    protected function assignVehicleToDriver($driverId,$vehicleId,$role): void
    {
        // deactivate previous active vehicle of same role
        DB::table('driver_vehicles')
            ->where('driver_id', $driverId)
            ->where('role', $role)
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'unassigned_at' => now(),
            ]);

        DB::table('driver_vehicles')->insert([
            'driver_id' => $driverId,
            'vehicle_id' => $vehicleId,
            'role' => $role, // Truck | Trailer
            'assigned_at' => now(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function addFiles($data,$incident)
    {
        $this->storeFiles($incident, $data['files'], $data['type'] ?? null);
        return $incident->load('files');
    }
    protected function storeFiles(Incident $incident, array $files, $type=null): void
    {
        foreach ($files as $file) {
            $path = $file->storeAs(
                'driver-incidents',
                Str::orderedUuid().rand(1,500).'.'.$file->getClientOriginalExtension(),
                'public'
            );

            $incident->files()->create([
                'type'=>$type,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }
    }
}
