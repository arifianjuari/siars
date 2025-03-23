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
        Schema::create('snars_assessment_elements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20)->comment('Kode elemen penilaian');
            $table->text('description')->comment('Deskripsi elemen penilaian');
            $table->text('assessment_guide')->nullable()->comment('Panduan penilaian');
            $table->integer('order')->default(0)->comment('Urutan elemen penilaian dalam bab');
            $table->boolean('is_active')->default(true)->comment('Status aktif');
            $table->enum('compliance_status', ['not_assessed', 'compliant', 'partially_compliant', 'non_compliant'])
                  ->default('not_assessed')
                  ->comment('Status pemenuhan elemen penilaian');
            $table->text('compliance_notes')->nullable()->comment('Catatan pemenuhan');
            $table->date('last_assessment_date')->nullable()->comment('Tanggal penilaian terakhir');
            $table->uuid('chapter_id')->comment('ID bab SNARS');
            $table->foreign('chapter_id')->references('id')->on('snars_chapters')->onDelete('cascade');
            $table->unsignedBigInteger('assessed_by')->nullable()->comment('ID user yang melakukan penilaian');
            $table->foreign('assessed_by')->references('id')->on('users');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->timestamps();
            
            // Composite unique key untuk kode elemen penilaian dalam satu bab
            $table->unique(['code', 'chapter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_assessment_elements');
    }
};
