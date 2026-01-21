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
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('type_id');
            $table->string('unit_number')->nullable();
            $table->string('status')->default('active'); //active.inactive,maintenance,out_of_service
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->integer('year')->nullable();
            $table->string('vin')->unique()->nullable();
            $table->string('plate')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->timestamp('expire_at')->nullable();
            $table->timestamp('inspection_at')->nullable();

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
