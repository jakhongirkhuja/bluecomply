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
        Schema::create('employment_verification_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employment_verification_id');
            $table->enum('type', ['sent','follow_up','response','internal_update']);
            $table->enum('method', ['email','fax','phone','manual','system'])->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_verification_events');
    }
};
