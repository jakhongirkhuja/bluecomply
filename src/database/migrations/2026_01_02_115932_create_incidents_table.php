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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('identifier');
            $table->text('type'); // ['accident','citations','inspections','clean','violations','claims','other_damage','other_incidents']
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->cascadeOnDelete();
            $table->unsignedBigInteger('company_id');
            $table->date('date');
            $table->time('time')->nullable();
            $table->time('time_end')->nullable();

            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('state_id')->nullable();
            $table->string('zip')->nullable();


            $table->string('truck')->nullable(); // assets | manual
            $table->unsignedBigInteger('truck_id')->nullable();
            $table->string('truck_unit_number')->nullable();
            $table->string('truck_make')->nullable();
            $table->string('truck_vin')->nullable();
            $table->string('truck_plate')->nullable();
            $table->unsignedBigInteger('truck_plate_state_id')->nullable();

            $table->string('trailer')->nullable(); // assets | manual
            $table->unsignedBigInteger('trailer_id')->nullable();
            $table->string('trailer_unit_number')->nullable();
            $table->string('trailer_make')->nullable();
            $table->string('trailer_vin')->nullable();
            $table->string('trailer_plate')->nullable();
            $table->unsignedBigInteger('trailer_plate_state_id')->nullable();


            $table->text('description')->nullable();

            $table->boolean('dot_reportable')->nullable();
            $table->boolean('injuries')->nullable();
            $table->jsonb('injury_types')->nullable();
            $table->boolean('at_fault')->nullable();
            $table->boolean('preventable')->nullable();
            $table->boolean('fatalities')->nullable();

            $table->boolean('third_party_required')->nullable();
            $table->string('third_party_name')->nullable();
            $table->string('third_party_contact')->nullable();
            $table->string('third_party_notes')->nullable();



            $table->boolean('tow_required')->nullable();
            $table->string('towing_company_name')->nullable();
            $table->string('towing_company_contact')->nullable();
            $table->string('towing_company_address')->nullable();



            $table->boolean('police_involved')->nullable(); // if true
            $table->string('police_report_number')->nullable();
            $table->boolean('hazmat_release')->nullable();


            $table->jsonb('damage_category')->nullable();
            $table->text('accident_description')->nullable();
//
            $table->unsignedBigInteger('damage_category_id')->nullable();
//            $table->unsignedBigInteger('specific_category_id')->nullable();

            $table->boolean('post_accident_test')->nullable();
            $table->text('test_explanation')->nullable(); //  if abowe is 0











            $table->unsignedBigInteger('citation_category_id')->nullable();
            $table->unsignedBigInteger('issuing_agency_id')->nullable();
            $table->string('citation_number')->nullable();
            $table->text('citation_notes')->nullable();
            $table->decimal('citation_amount', 10, 2)->default(0);
            $table->string('officer_name')->nullable();
            $table->date('court_date')->nullable();
            $table->boolean('lawyer_hired')->default(false);
            $table->string('lawyer_name')->nullable();
            $table->string('lawyer_contact')->nullable();


            $table->string('report_number')->nullable();
            $table->string('shipper_name')->nullable();
            $table->unsignedBigInteger('inspection_level_id')->nullable();
            $table->boolean('accident_related')->nullable();

            $table->string('status')->default('open'); //open/closed/under investigation/pending

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
