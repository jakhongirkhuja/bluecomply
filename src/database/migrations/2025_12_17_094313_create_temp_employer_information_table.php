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

            $table->unsignedBigInteger('driver_id');

            $table->string('type_engagement')->default('job');
            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('address');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('current_employer')->default(true); // if current false , then reason for leaving must requried
            $table->text('reason_for_leaving')->nullable();

            $table->string('company_contact_name')->nullable();
            $table->string('company_contact_phone')->nullable();
            $table->string('company_contact_email')->nullable();
            $table->boolean('company_contact_allow')->default(true);


            $table->boolean('safety_regulations')->default(false);
            $table->boolean('sensitive_functions')->default(false);
            $table->boolean('motor_vehicle')->default(false);
            $table->string('type')->nullable();
            $table->json('equipment_operated')->nullable();
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
