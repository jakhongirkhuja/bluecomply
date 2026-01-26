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
        Schema::create('dataq_challenges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('request_id')->unique()->comment('RQ123456789');
            $table->string('status'); // Pending, Resolved, Denied

            $table->foreignId('incident_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('accident_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('truck_id')->nullable()->constrained()->onDelete('set null');

            // Поля для "Add Manually" (если записи нет в системе)
            $table->string('report_number')->nullable();
            $table->string('state_id')->nullable();
            $table->string('manual_equipment_unit')->nullable();

            // Тип и категория челленджа
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('category_id');

            $table->text('explanation');
            $table->text('internal_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dataq_challenges');
    }
};
