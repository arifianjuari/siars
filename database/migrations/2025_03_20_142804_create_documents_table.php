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
            $table->uuid('required_document_id')->comment('ID dokumen yang diperlukan');
            $table->string('title')->comment('Judul dokumen');
            $table->string('file_path')->comment('Path file dokumen');
            $table->string('file_name')->comment('Nama file asli');
            $table->string('file_type')->comment('Tipe file (mime type)');
            $table->integer('file_size')->comment('Ukuran file dalam bytes');
            $table->string('version', 10)->default('1.0')->comment('Versi dokumen');
            $table->boolean('is_current_version')->default(true)->comment('Apakah versi terbaru');
            $table->text('notes')->nullable()->comment('Catatan dokumen');
            $table->date('document_date')->nullable()->comment('Tanggal dokumen');
            $table->date('expiry_date')->nullable()->comment('Tanggal kadaluarsa dokumen');
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'expired'])
                  ->default('draft')
                  ->comment('Status dokumen');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('ID user yang menyetujui');
            $table->timestamp('approved_at')->nullable()->comment('Waktu persetujuan');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('required_document_id')->references('id')->on('required_documents')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            // Indexes
            $table->index('status');
            $table->index('is_current_version');
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
