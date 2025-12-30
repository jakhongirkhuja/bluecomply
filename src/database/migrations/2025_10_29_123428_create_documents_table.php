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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('driver_id');
            $table->foreignId('category_id')->constrain('document_categories');
            $table->foreignId('document_type_id')->constrained();
            $table->string('cdlclasses_id')->nullable();
            $table->enum('side', ['front','back'])->nullable();
            $table->string('name')->nullable();
            $table->string('number')->nullable();
            $table->enum('status', [
                'valid','expiring','expired','pending_review','missing'
            ])->default('pending_review');
            $table->boolean('current')->default(false);


            $table->date('issue_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->enum('uploaded_by', ['driver','company_owner']);
            $table->boolean('is_encrypted')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'document_type_id', 'side','driver_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
