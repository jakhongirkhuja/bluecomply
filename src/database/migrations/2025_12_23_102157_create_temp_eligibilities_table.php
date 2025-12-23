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
        Schema::create('eligibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->unsignedBigInteger('driver_id');
            $table->boolean('disqualified')->default(false);
            $table->boolean('suspended_revoked')->default(false);
            $table->boolean('license_denied')->default(false);
            $table->boolean('dot_test')->default(false);
            $table->boolean('violations_past_3_years')->default(false);
            $table->boolean('minor_traffic')->default(false);
            $table->boolean('pending_charges')->default(false);
            $table->boolean('violations_past_5_years')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_eligibilities');
    }
};
