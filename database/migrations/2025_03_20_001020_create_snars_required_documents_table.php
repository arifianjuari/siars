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
        Schema::create('snars_required_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('element_id');
            $table->uuid('document_type_id');
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->foreign('element_id')->references('id')->on('snars_assessment_elements');
            $table->foreign('document_type_id')->references('id')->on('snars_document_types');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            $table->unique(['element_id', 'document_type_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_required_documents');
    }
};
