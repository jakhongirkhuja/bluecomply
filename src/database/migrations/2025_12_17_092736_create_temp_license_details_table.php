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
            $table->string('type')->default('dl'); //cdl,dl,stateId
            $table->foreignId('document_id')
                ->nullable()
                ->after('id')
                ->constrained('documents')
                ->nullOnDelete();
            $table->string('license_type')->nullable();
            $table->unsignedBigInteger('driver_id');
            $table->string('license_number');
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('state_id');
            $table->date('license_issue_date')->nullable();
            $table->date('license_expiration');
            $table->string('driver_license_front_path')->nullable();
            $table->string('driver_license_back_path')->nullable();
            $table->boolean('current')->default(true);


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
