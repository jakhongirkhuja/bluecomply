<?php

namespace App\Services\Company;

use App\Models\Company\Company;
use App\Models\Company\Note;

class NoteService
{
    public function create($data)
    {
       return  Note::create($data);
    }
    public function update(Note $note, array $data)
    {
        $note->update($data);
        return $note;
    }
    public function filterEligibleUsers($userIds, $companyOwnerId)
    {

        $companyId = Company::where('user_id', $companyOwnerId)->value('id');

        $eligibleUsers = [];
        foreach ($userIds as $userId) {
            $company = Company::where('user_id', $userId)
                ->where('id', $companyId)
                ->exists();

            if ($company) {
                $eligibleUsers[] = $userId;
            }
        }

        return $eligibleUsers;
    }
}
