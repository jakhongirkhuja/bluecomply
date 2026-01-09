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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('employee_id')->unique();
            $table->string('primary_phone')->nullable();
            $table->boolean('position_dot')->default(false);
            $table->string('rand_number')->nullable();
            $table->timestamp('phone_confirm_at')->nullable();
            $table->timestamp('phone_confirm_sent')->nullable();
            $table->uuid('driver_temp_token')->nullable();
            $table->text('status')->default('new');
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('ssn_sin')->unique()->nullable();
            $table->date('date_of_birth')->nullable();
            $table->timestamp('hired_at')->nullable();
            $table->boolean('random_pool')->default(false);
            $table->boolean('mvr_monitor')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
