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
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('identifier');
            $table->unsignedBigInteger('incident_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('company_id');

            $table->text('type')->nullable(); // Liability , Physical Damage, Cargo, Subrogation, Trailer Interchange, Other
            $table->text('other_type')->nullable();

            //Liability , Physical Damage, Cargo, Subrogation, Trailer Interchange, Other
            $table->string('claim_number')->nullable();
            $table->string('carrier_name')->nullable();
            $table->string('adjuster_name')->nullable();
            $table->string('adjuster_contact')->nullable();

            $table->enum('status', ['Open', 'Reviewing', 'Paid', 'Denied', 'Closed'])->default('Open');
            $table->decimal('deductible_amount', 10, 2)->default(0);
            $table->decimal('insurance_paid', 10, 2)->default(0);
            //  end Liability, Physical Damage, Cargo, Subrogation, Trailer Interchange, Other

            //Liability ,
            $table->string('opposing_party_name')->nullable();
            $table->string('opposing_party_insurance')->nullable();
            // end Liability

            //Physical Damage, Trailer Interchange
            $table->string('repair_vendor_name')->nullable();
            // end Physical Damage, Trailer Interchange


            //Cargo ,
            $table->string('shipper_name')->nullable();
            $table->string('damage_type')->nullable();
            $table->decimal('cargo_value', 10, 2)->default(0);
            $table->decimal('cargo_loss_amount', 10, 2)->default(0);
            // end Cargo

            //Subrogation
            $table->string('internal_claim_number')->nullable();
            $table->string('opposing_carrier_name')->nullable();
            // end Subrogation

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
