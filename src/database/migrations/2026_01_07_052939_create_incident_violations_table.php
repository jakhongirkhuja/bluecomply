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
        Schema::create('incident_violations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('incident_id');
            $table->unsignedBigInteger('driver_id');
            $table->string('code');
            $table->string('unit');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('violation_category_id');
            $table->boolean('violation_oos')->default(false);
            $table->boolean('violation_corrected')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_violations');
    }
};
