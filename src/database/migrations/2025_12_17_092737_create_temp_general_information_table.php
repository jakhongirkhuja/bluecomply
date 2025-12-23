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
        Schema::create('general_information', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('driver_id');


            $table->boolean('license_denial')->default(false);
            $table->boolean('has_driving_convictions')->default(false);
            $table->boolean('has_substance_conviction')->default(false);
            $table->boolean('positive_substance_violation')->default(false);
            $table->boolean('has_moving_violation_or_accident_last_3_years')->default(false);
            $table->boolean('has_violations_accidents')->default(false);
            $table->boolean('eligible_for_us_employment')->default(true);
            $table->boolean('speak_english')->default(true);

//
//            $table->string('position_applied_for');
//            $table->unsignedBigInteger('driver_id');
//            $table->boolean('eligible_for_us_employment');
//            $table->boolean('currently_employed');
//
//            $table->boolean('worked_before');
//            $table->boolean('twic_card');
//            $table->string('hear_about_us');
//            $table->string('driver_name')->nullable();
//            $table->string('other')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_general_information');
    }
};
