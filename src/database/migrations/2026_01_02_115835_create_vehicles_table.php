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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->text('type');//['Truck','Trailer','Cargo','Equipment','Other']
            $table->string('unit_number')->nullable();
            $table->string('make')->nullable();
            $table->string('vin')->nullable();
            $table->string('plate')->nullable();
            $table->string('plate_state')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
