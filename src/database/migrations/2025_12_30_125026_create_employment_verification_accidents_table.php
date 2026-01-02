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
        Schema::create('employment_verification_accidents', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('response_id');
            $table->date('accident_date');
            $table->boolean('dot_recordable')->default(false);
            $table->boolean('preventable')->default(false);
            $table->string('city')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->integer('injuries')->nullable();
            $table->integer('fatalities')->nullable();
            $table->boolean('hazardous_material_involved')->default(false);
            $table->string('equipment_driven')->nullable();
            $table->text('description')->nullable();
            $table->text('comments')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_verification_accidents');
    }
};
