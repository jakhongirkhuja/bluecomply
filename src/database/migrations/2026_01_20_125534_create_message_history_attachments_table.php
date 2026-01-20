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
        Schema::create('message_history_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_history_id');
            $table->string('file_name');
            $table->string('file_path')->unique();
            $table->unsignedInteger('file_size');
            $table->string('mime_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_history_attachments');
    }
};
