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
        Schema::create('driver_signs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('driver_id');
            $table->string('sign_path')->nullable();
            $table->boolean('checked')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_driver_signs');
    }
};
