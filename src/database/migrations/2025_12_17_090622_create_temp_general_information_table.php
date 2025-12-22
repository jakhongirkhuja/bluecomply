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
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->string('position_applied_for');
            $table->unsignedBigInteger('driver_id');
            $table->boolean('eligible_for_us_employment');
            $table->boolean('currently_employed');
            $table->boolean('english');
            $table->boolean('worked_before');
            $table->boolean('twic_card');
            $table->string('hear_about_us');
            $table->string('driver_name')->nullable();
            $table->string('other')->nullable();
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
