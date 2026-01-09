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
        Schema::create('random_selections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('company_id');
            $table->text('service');//urine_drug', 'alcohol
            $table->boolean('is_dot')->default(true);
            $table->date('selected_at');
            $table->foreignId('random_pool_membership_id')->nullable();
            $table->foreignId('drug_test_order_id')->nullable();
            $table->text('status')->default('selected'); //
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('random_selections');
    }
};
