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
        Schema::create('employer_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->unsignedBigInteger('driver_id');
            $table->boolean('employed')->default(false);
            $table->string('company_name')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->string('street_address')->nullable();
            $table->string('zip_postal')->nullable();
            $table->string('telephone')->nullable();
            $table->string('position_held')->nullable();
            $table->text('reason_for_leaving')->nullable();
            $table->boolean('terminated')->default(false);
            $table->boolean('current_employer')->default(false);
            $table->boolean('safety_regulations')->default(false);
            $table->boolean('sensitive_functions')->default(false);
            $table->boolean('motor_vehicle')->default(false);
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_employer_information');
    }
};
