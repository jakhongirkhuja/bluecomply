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
        Schema::create('accidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->unsignedBigInteger('driver_id');
            $table->boolean('convictions_past_years')->default(false);
            $table->date('accident_date')->nullable();
            $table->string('accident_type')->nullable();
            $table->boolean('hazmat_involved')->default(false);
            $table->boolean('vehicle_towed')->default(false);
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->boolean('commercial_vehicle')->default(false);
            $table->boolean('fault')->default(false);
            $table->boolean('ticketed')->default(false);
            $table->text('details'); // Detailed description
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_accidents');
    }
};
