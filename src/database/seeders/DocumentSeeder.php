<?php

namespace Database\Seeders;

use App\Models\Company\Cdlclass;
use App\Models\Company\DocumentCategory;
use App\Models\Company\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DocumentCategory::insert([
            [
                'name' => 'Identity & Licensing',
                'slug' => 'identity',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Employment',
                'slug' => 'employment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Background Checks',
                'slug' => 'background',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Safety & Training',
                'slug' => 'safety',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Other Documents',
                'slug' => 'other',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        $identity = DocumentCategory::where('slug', 'identity')->first()->id;
        $background = DocumentCategory::where('slug', 'background')->first()->id;
        $other = DocumentCategory::where('slug', 'other')->first()->id;

        DocumentType::insert([
            // Identity & Licensing
            [
                'category_id' => $identity,
                'name' => 'CDL',
                'requires_expiry' => true,
                'requires_review' => true,
                'is_required' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $identity,
                'name' => 'Driver License',
                'requires_expiry' => true,
                'requires_review' => true,
                'is_required' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $identity,
                'name' => 'Passport / Green Card / Work Permit',
                'requires_expiry' => true,
                'requires_review' => false,
                'is_required' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $identity,
                'name' => 'Medical Card',
                'requires_expiry' => true,
                'requires_review' => false,
                'is_required' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Background Checks
            [
                'category_id' => $background,
                'name' => 'MVR',
                'requires_expiry' => true,
                'requires_review' => false,
                'is_required' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Other Documents
            [
                'category_id' => $other,
                'name' => 'SSN',
                'requires_expiry' => false,
                'requires_review' => false,
                'is_required' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $other,
                'name' => 'Other',
                'requires_expiry' => false,
                'requires_review' => false,
                'is_required' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Cdlclass::insert([
            [
                'name' => 'Class A',
                'short'=>'A',
            ],
            [
                'name' => 'Class B',
                'short'=>'B',
            ],
            [
                'name' => 'Class C',
                'short'=>'C',
            ],
        ]);
    }
}
