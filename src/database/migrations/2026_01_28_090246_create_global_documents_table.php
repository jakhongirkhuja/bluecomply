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
        Schema::create('global_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_id');
            $table->string('source_table');                  // insurance, compliance, driver, fleet
            $table->string('company_id');
            $table->string('name');
            $table->string('category');                       // Insurance / Company / Driver / Fleet
            $table->string('type');                           // Physical Damage, CDL License, etc.
            $table->string('related_to')->nullable();        // Policy #, MC #, Unit #, Person

            // Dates
            $table->date('upload_date')->default(now());
            $table->date('expiration')->nullable();

            $table->string('status')->default('active');                         // Valid / Expiring Soon / Expired
            $table->string('uploaded_by_id')->nullable();
            $table->string('uploaded_by_table_name')->nullable();

            $table->index(['category', 'type']);
            $table->index('expiration');
            $table->index('source_table');
            $table->index('company_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_documents');
    }
};
