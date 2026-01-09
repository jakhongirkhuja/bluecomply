<?php

namespace App\Services\I3Screen;

use App\Models\Company\AuditLog;
use App\Models\Company\RandomPoolMembership;
use App\Models\Company\RandomSelection;
use Illuminate\Support\Facades\DB;

class RandomDrawService
{
    public function run(int $companyId, string $service, int $count)
    {
        DB::transaction(function () use ($companyId, $service, $count) {


            $drivers = RandomPoolMembership::where('company_id', $companyId)
                ->where('service', $service)
                ->where('status', 'active')
                ->inRandomOrder()
                ->limit($count)
                ->get();
            $newdrive = [];
            foreach ($drivers as $membership) {


                $selection = RandomSelection::create([
                    'driver_id' => $membership->driver_id,
                    'company_id' => $membership->company_id,
                    'service' => $membership->service,
                    'is_dot' => $membership->is_dot,
                    'selected_at' => now(),
                    'status' => 'selected',
                ]);
                $newdrive[] = $membership->driver_id;

            }
//            AuditLog::log(
//                subject: $newdrive,
//                action: 'RANDOM POOL MEMBERS SELECTED',
//                details: 'Drivers selected successfully'
//            );
        });
    }
}
