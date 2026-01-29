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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('tenet_id')->unique();
            $table->string('dot_number')->unique();
            $table->text('logo')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->text('status')->default('active');
            $table->string('der_name')->nullable();
            $table->string('der_last_name')->nullable();
            $table->string('der_email')->nullable();
            $table->string('der_phone')->nullable();
            $table->string('der_address')->nullable();
            $table->string('der_alternative_phone')->nullable();
            $table->timestamp('last_active')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->unsignedBigInteger('drivers')->default(0);
            $table->unsignedBigInteger('all_drivers')->default(0);

            $table->boolean('claims_modal')->default(false);
            $table->boolean('roadside_inspections')->default(false);
            $table->boolean('drug_alcohol_testing')->default(false);
            $table->boolean('mvr_ordering')->default(false);
            $table->boolean('bulk_driver_import')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
