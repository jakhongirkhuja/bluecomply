<?php

namespace App\Services\Driver;

use App\Models\Driver\DrugTest;

class DrugTestService
{
    public function create(array $data): DrugTest
    {
        return DrugTest::create($data);
    }

    public function update(DrugTest $drugTest, array $data): DrugTest
    {
        $drugTest->update($data);
        return $drugTest;
    }

    public function delete(DrugTest $drugTest): bool
    {
        return $drugTest->delete();
    }
}
