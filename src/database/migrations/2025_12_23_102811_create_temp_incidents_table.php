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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->unsignedBigInteger('driver_id');
            $table->boolean('convictions_past_years')->default(false);
            $table->date('violation_date')->nullable();
            $table->string('charge_description')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->boolean('commercial_vehicle')->default(false);
            $table->json('penalties')->nullable();
            $table->text('fine_amount')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_incidents');
    }
};
