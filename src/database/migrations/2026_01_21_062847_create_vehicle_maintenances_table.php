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
        Schema::create('vehicle_maintenances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('vehicle_maintenance_type_id');
            $table->unsignedBigInteger('company_id');
            $table->date('service_date');
            $table->integer('mileage');
            $table->string('vendor_name');
            $table->text('description')->nullable();
            $table->text('next_due_type')->default('miles'); //miles,date
            $table->date('next_due_date')->nullable();
            $table->text('status')->default('active');
            $table->boolean('current')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenances');
    }
};
