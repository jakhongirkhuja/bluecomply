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
        Schema::create('license_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->unsignedBigInteger('driver_id');
            $table->string('license_number');
            $table->string('licensing_authority');
            $table->date('license_expiration');
            $table->date('dot_medical_card')->nullable();
            $table->boolean('is_commercial');
            $table->boolean('is_current_license');
            $table->json('endorsements')->nullable();
            $table->string('medical_card_path')->nullable();
            $table->string('driver_license_front_path')->nullable();
            $table->string('driver_license_back_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_license_details');
    }
};
