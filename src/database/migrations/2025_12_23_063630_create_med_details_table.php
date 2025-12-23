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
        Schema::create('med_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->date('med_issue_date')->nullable();
            $table->date('med_expiration')->nullable();
            $table->string('med_path')->nullable();
            $table->boolean('current')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('med_details');
    }
};
