<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employment_verification_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employment_verification_id');


            $table->string('position_held')->nullable();
            $table->string('driver_class')->nullable();
            $table->string('driver_type')->nullable();
            $table->boolean('eligible_for_rehire')->default(false);
            $table->boolean('was_terminated')->default(false);
            $table->string('termination_reason')->nullable();
            $table->boolean('fmcsr_subject')->default(false);
            $table->boolean('safety_sensitive_job')->default(false);
            $table->string('area_driven')->nullable();
            $table->string('equipment_driven')->nullable();
            $table->string('trailer_driven')->nullable();
            $table->string('loads_hailed')->nullable();


            $table->boolean('alcohol_0_04_or_higher')->default(false);
            $table->boolean('verified_positive_drug_test')->default(false);
            $table->boolean('refused_test')->default(false);
            $table->boolean('other_dot_violation')->default(false);
            $table->boolean('reported_previous_violation')->default(false);
            $table->boolean('return_to_duty_completed')->default(false);
            $table->text('drug_alcohol_comments')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_verification_responses');
    }
};
