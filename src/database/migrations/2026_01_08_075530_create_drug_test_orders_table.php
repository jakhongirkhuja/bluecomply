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
        Schema::create('drug_test_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('company_id');
            $table->string('reference_id')->unique();
            $table->string('i3_case_number')->nullable();
            $table->text('test_type'); //drug,alcohol,drug_alcohol
            $table->text('reason'); //PRE-EMPLOYMENT,RANDOM,POST-ACCIDENT,REASONABLE SUSPICION/CAUSE,RETURN TO DUTY,FOLLOW-UP'
            $table->string('dot_agency')->nullable(); // FMCSA
            $table->boolean('observed')->default(false);
            $table->date('expiration_date')->nullable();
            $table->string('package_code')->nullable();
            $table->text('status')->default('created'); //'created','scheduled','in_progress', 'completed', 'cancelled'
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drug_test_orders');
    }
};
