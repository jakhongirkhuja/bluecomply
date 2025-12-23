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
        Schema::create('driving_experiences', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('driver_id');

            $table->unsignedTinyInteger('years_of_experience');
            $table->unsignedBigInteger('miles_driven');
            $table->date('from');
            $table->date('to');
            $table->string('equipment_operated')->nullable();
            $table->unsignedBigInteger('state_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_driving_experiences');
    }
};
