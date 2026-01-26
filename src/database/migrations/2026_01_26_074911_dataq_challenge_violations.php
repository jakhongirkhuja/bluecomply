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
        Schema::create('dataq_challenge_violations', function (Blueprint $table) {
            $table->unsignedBigInteger('dataq_challenge_id');
            $table->unsignedBigInteger('violation_id');
            $table->index(['dataq_challenge_id','violation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dataq_challenge_violations');
    }
};
