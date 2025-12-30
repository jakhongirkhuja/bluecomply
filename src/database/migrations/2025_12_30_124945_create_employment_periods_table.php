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
        Schema::create('employment_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('company_id');

            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active','terminated'])->default('active');
            $table->string('termination_reason')->nullable();
            $table->boolean('rehired')->default(false);
            $table->text('notes')->nullable();
            $table->boolean('notify_driver')->default(false);
            $table->date('payed_date')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_periods');
    }
};
