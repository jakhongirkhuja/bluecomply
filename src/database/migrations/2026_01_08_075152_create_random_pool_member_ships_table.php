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
        Schema::create('random_pool_memberships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('company_id');
            $table->year('year');
            $table->date('date')->nullable();
            $table->text('service');  // urine_drug, alcohol
            $table->boolean('is_dot')->default(true);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('note')->nullable();

            $table->string('status')->default('active');   // active, inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('random_pool_member_ships');
    }
};
