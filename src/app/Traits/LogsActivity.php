<?php

namespace App\Traits;

use App\Models\Company\ActivityLog;

trait  LogsActivity
{
    public function logActivity(string $action, $details, $user_type='admin', $driver_id=null,$action_table_name,$action_id): ActivityLog
    {

        return ActivityLog::create([
            'action'       => $action,
            'details'      => $details,
            'action_at'    => now(),
            'user_type'    => $user_type,
            'user_id'      =>$user_type=='admin'? auth()->id() : null,
            'driver_id' => $driver_id,
            'action_table_name'=>$action_table_name,
            'action_id'=>$action_id,
        ]);
    }
}
