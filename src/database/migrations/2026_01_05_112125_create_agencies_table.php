<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // California Highway Patrol
            $table->string('short_name')->nullable(); // CHP, DPS, NYPD
            $table->enum('level', ['city', 'county', 'state', 'federal', 'commercial'])->nullable();
            $table->string('state', 2)->nullable();  // CA, TX (nullable for federal)
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
