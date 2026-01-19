<?php

namespace App\Services\Company;

use App\Http\Requests\Company\StoreTaskRequest;
use App\Models\Company\Task;

class DriverTaskService
{
    public function addTask($data){
        $taskData = [
            'driver_id'    => $data['driver_id'],
            'assigned_by'  => $data['assigned_by'],
            'title'        => $data['title'] ?? 'Compliance Task',
            'description'  => $data['description'] ?? null,
            'category'     => 'compliance',
            'status'       => 'pending',
            'priority'     => $data['priority'] ?? 'medium',
            'related_type' => $data['related_type'] ?? null,
            'related_id'   => $data['related_id'] ?? null,
            'due_date'     => $data['due_date'] ?? null,
            'company_id'   => $data['company_id'] ?? null,
        ];
        return Task::create($taskData);
    }
}
