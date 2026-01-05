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
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incident_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('company_id');

            $table->text('type')->nullable(); // ["Physical Damage","Cargo","Trailer Interchange","Liability"]
            $table->text('other_type')->nullable();
            $table->string('claim_number')->nullable();
            $table->string('carrier_number')->nullable();
            $table->string('adjuster_name')->nullable();
            $table->string('adjuster_contact')->nullable();

            $table->enum('status', ['Open', 'Reviewing', 'Paid', 'Denied','Closed'])->default('Open');
            $table->decimal('deductible_amount', 10, 2)->default(0);
            $table->decimal('insurance_paid', 10, 2)->default(0);
            $table->string('opposing_party_name')->nullable();
            $table->string('opposing_party_insurance')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
