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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('registration_link_id');
            $table->unsignedBigInteger('driver_id');
            $table->integer('step')->default(1);
            $table->boolean('submitted')->default(false);
            $table->json('application_data')->nullable();
            $table->text('confirmation_number')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->ipAddress('used_ip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_applications');
    }
};
