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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('assigned_by');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', ['onboarding','compliance','manual', 'completed'])->default('manual');
            $table->enum('status', ['pending', 'in_progress','completed'])->default('pending');
            $table->unsignedBigInteger('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->text('priority')->default('medium');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
