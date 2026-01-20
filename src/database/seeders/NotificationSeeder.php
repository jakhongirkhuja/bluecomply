<?php

namespace Database\Seeders;

use App\Models\Company\Company;
use App\Models\Company\Document;
use App\Models\Company\Notification;
use App\Models\Driver\Driver;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $companies = Company::all();
        $drivers = Driver::all();
        $documents = Document::all();

        if ($users->isEmpty() || $companies->isEmpty() || $drivers->isEmpty()) {
            $this->command->info("Users, Companies, or Drivers not found. Run user/company/driver seeders first.");
            return;
        }

        foreach ($users as $user) {

            // Create 10 notifications per user
            for ($i = 1; $i <= 10; $i++) {

                $company = $companies->random();
                $driver = $drivers->random();
                $document = $documents->random();

                Notification::create([
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'driver_id' => $driver->id,
                    'document_id' => $document->id,
                    'type' => ['compliance','drug_test','dq_file','easy_ev'][array_rand(['compliance','drug_test','dq_file','easy_ev'])],
                    'title' => $this->randomTitle(),
                    'message' => $this->randomMessage(),
                    'level' => ['critical','warning','info'][array_rand(['critical','warning','info'])],
                    'status' => ['unread','read'][array_rand(['unread','read'])],
                ]);
            }

        }

    }
    private function randomTitle()
    {
        $titles = [
            "CDL Expires soon",
            "Medical Card Expires in soon",
            "Overdue by 3 Days",
            "Document Uploaded — Pending Review",
            "Response Received",
            "Drug Test Result: Negative",
            "Driver Updated Profile",
            "Compliance Document Missing",
        ];

        return $titles[array_rand($titles)];
    }

    private function randomMessage()
    {
        $messages = [
            "Please review the document and update status.",
            "Driver needs to renew the document.",
            "New upload is pending approval.",
            "System detected overdue document.",
            "Driver updated details successfully.",
            "Result is negative. No action required.",
            "Compliance check required.",
        ];

        return $messages[array_rand($messages)];
    }
}
