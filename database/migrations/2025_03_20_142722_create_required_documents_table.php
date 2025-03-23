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
        Schema::create('required_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('assessment_element_id')->comment('ID elemen penilaian');
            $table->uuid('document_type_id')->comment('ID jenis dokumen');
            $table->string('name')->comment('Nama dokumen yang diperlukan');
            $table->text('description')->nullable()->comment('Deskripsi dokumen');
            $table->boolean('is_mandatory')->default(true)->comment('Apakah dokumen wajib');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('assessment_element_id')->references('id')->on('snars_assessment_elements')->onDelete('cascade');
            $table->foreign('document_type_id')->references('id')->on('document_types');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            // Unique constraint untuk menghindari duplikasi dokumen yang sama untuk satu elemen penilaian
            $table->unique(['assessment_element_id', 'document_type_id', 'name'], 'unique_required_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('required_documents');
    }
};
