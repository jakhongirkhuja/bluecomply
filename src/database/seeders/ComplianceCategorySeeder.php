<?php

namespace Database\Seeders;

use App\Models\General\ComplianceCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComplianceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Operating Authority',
            'W9',
            'EIN',
            'Agreement',
            'Permit',
            'Other',
        ];

        foreach ($categories as $category) {
            ComplianceCategory::create(['name' => $category]);
        }
    }
}
