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
        Schema::create('driver_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->unsignedBigInteger('driver_id');
            $table->boolean('attended')->default(false);
            $table->string('type')->nullable();
            $table->string('name')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('state_id')->nullable();
            $table->string('city_id')->nullable();
            $table->string('address');
            $table->string('address2')->nullable();
            $table->string('telephone')->nullable();
            $table->boolean('graduated')->default(false);
            $table->boolean('safety_regulations')->default(false);
            $table->boolean('sensitive_functions')->default(false);
            $table->string('gpa')->nullable();
            $table->string('hours_instruction')->nullable();
            $table->json('skills_trained')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_driver_trainings');
    }
};
