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
        Schema::create('snars_supporting_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->comment('Judul dokumen');
            $table->text('description')->nullable()->comment('Deskripsi dokumen');
            $table->string('file_path')->comment('Path file dokumen');
            $table->string('file_name')->comment('Nama file asli');
            $table->string('file_type')->comment('Tipe file (MIME type)');
            $table->integer('file_size')->comment('Ukuran file dalam bytes');
            $table->integer('version')->default(1)->comment('Versi dokumen');
            $table->enum('status', ['draft', 'review', 'approved', 'rejected'])->default('draft')->comment('Status dokumen');
            $table->text('rejection_reason')->nullable()->comment('Alasan penolakan jika status rejected');
            $table->uuid('assessment_element_id')->comment('ID elemen penilaian');
            $table->foreign('assessment_element_id')->references('id')->on('snars_assessment_elements')->onDelete('cascade');
            $table->uuid('parent_id')->nullable()->comment('ID dokumen induk (untuk versioning)');
            $table->foreign('parent_id')->references('id')->on('snars_supporting_documents')->onDelete('set null');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('ID user yang menyetujui dokumen');
            $table->foreign('approved_by')->references('id')->on('users');
            $table->timestamp('approved_at')->nullable()->comment('Waktu persetujuan dokumen');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_supporting_documents');
    }
};
