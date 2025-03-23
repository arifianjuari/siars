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
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('document_number', 100);
            $table->string('title', 255);
            $table->uuid('document_type_id');
            $table->string('version', 20);
            $table->string('status', 50); // Draft, Review, Approved, Obsolete
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('review_date')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_type', 50)->nullable();
            $table->uuid('department_id');
            $table->text('description')->nullable();
            $table->text('keywords')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->foreign('document_type_id')->references('id')->on('document_types');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            $table->unique(['tenant_id', 'document_number', 'version']);
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
