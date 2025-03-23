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
        Schema::create('assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('period_id')->comment('ID periode penilaian');
            $table->uuid('group_id')->nullable()->comment('ID kelompok SNARS');
            $table->uuid('chapter_id')->nullable()->comment('ID bab SNARS');
            $table->uuid('standard_id')->nullable()->comment('ID standar SNARS');
            $table->date('assessment_date')->comment('Tanggal penilaian');
            $table->enum('status', ['draft', 'in_progress', 'completed', 'verified'])
                  ->default('draft')
                  ->comment('Status penilaian');
            $table->text('notes')->nullable()->comment('Catatan penilaian');
            $table->unsignedBigInteger('assessed_by')->nullable()->comment('ID user yang melakukan penilaian');
            $table->unsignedBigInteger('verified_by')->nullable()->comment('ID user yang memverifikasi penilaian');
            $table->timestamp('verified_at')->nullable()->comment('Waktu verifikasi');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('period_id')->references('id')->on('assessment_periods')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('snars_groups');
            $table->foreign('chapter_id')->references('id')->on('snars_chapters');
            $table->foreign('standard_id')->references('id')->on('snars_standards');
            $table->foreign('assessed_by')->references('id')->on('users');
            $table->foreign('verified_by')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            // Indexes
            $table->index('status');
            $table->index('assessment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
