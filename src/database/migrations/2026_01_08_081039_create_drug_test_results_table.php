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
        Schema::create('drug_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drug_test_order_id')->constrained();
            $table->enum('result', ['NEGATIVE', 'POSITIVE', 'REFUSAL', 'CANCELLED']);
            $table->string('pdf_path');
            $table->timestamp('reported_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drug_test_results');
    }
};
