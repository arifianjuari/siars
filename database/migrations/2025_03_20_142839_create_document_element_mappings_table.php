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
        Schema::create('document_element_mappings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('document_id')->comment('ID dokumen');
            $table->uuid('assessment_element_id')->comment('ID elemen penilaian');
            $table->text('notes')->nullable()->comment('Catatan pemetaan');
            $table->boolean('is_primary')->default(false)->comment('Apakah dokumen utama untuk elemen ini');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('assessment_element_id')->references('id')->on('snars_assessment_elements')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            // Unique constraint untuk menghindari duplikasi pemetaan
            $table->unique(['document_id', 'assessment_element_id'], 'unique_document_element_mapping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_element_mappings');
    }
};
