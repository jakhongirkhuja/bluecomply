<?php

namespace Database\Seeders;

use App\Models\Driver\EmploymentVerification;
use App\Models\Driver\EmploymentVerificationResponse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmploymentVerificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $verifications = [1,2,3,4,5,6,7,8,9];

        foreach ($verifications as $verification) {
            $response = EmploymentVerificationResponse::create([
                'employment_verification_id' => 1,
                'position_held' => 'Driver',
                'driver_class_id' => 1,
                'driver_type' => 'Full-time',
                'eligible_for_rehire' => true,
                'was_terminated' => false,
                'fmcsr_subject' => true,
                'safety_sensitive_job' => true,
                'area_driven' => 'Regional',
                'equipment_driven' => 'Truck',
                'trailer_driven' => 'None',
                'loads_hailed' => 'Various',
                'alcohol_text_higher' => false,
                'verified_positive_drug_test' => false,
                'refused_test' => false,
                'other_dot_violation' => false,
                'reported_previous_violation' => false,
                'return_to_duty_completed' => false,
                'drug_alcohol_comments' => 'No violations',
            ]);

            $response->accidents()->create([
                'accident_date' => now()->subMonths(6),
                'dot_recordable' => true,
                'preventable' => false,
                'city' => 'New York',
                'state_id' => 1,
                'injuries' => 1,
                'fatalities' => 0,
                'hazardous_material_involved' => false,
                'equipment_driven' => 'Truck',
                'description' => 'Minor fender bender',
                'comments' => 'No further action',
            ]);
        }
    }
}
