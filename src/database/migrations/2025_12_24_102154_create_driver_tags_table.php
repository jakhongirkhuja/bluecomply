<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('driver_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comapny_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('user_id');
            $table->string('tag');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_tags');
    }
};
