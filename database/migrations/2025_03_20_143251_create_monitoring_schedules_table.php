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
        Schema::create('monitoring_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('period_id')->comment('ID periode penilaian');
            $table->uuid('group_id')->nullable()->comment('ID kelompok SNARS');
            $table->uuid('chapter_id')->nullable()->comment('ID bab SNARS');
            $table->uuid('standard_id')->nullable()->comment('ID standar SNARS');
            $table->string('title')->comment('Judul jadwal monitoring');
            $table->text('description')->nullable()->comment('Deskripsi jadwal monitoring');
            $table->date('scheduled_date')->comment('Tanggal terjadwal');
            $table->time('start_time')->nullable()->comment('Waktu mulai');
            $table->time('end_time')->nullable()->comment('Waktu selesai');
            $table->enum('frequency', ['once', 'daily', 'weekly', 'monthly', 'quarterly', 'yearly'])
                  ->default('once')
                  ->comment('Frekuensi monitoring');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])
                  ->default('scheduled')
                  ->comment('Status jadwal');
            $table->text('notes')->nullable()->comment('Catatan jadwal');
            $table->unsignedBigInteger('assigned_to')->nullable()->comment('ID user yang ditugaskan');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('period_id')->references('id')->on('assessment_periods');
            $table->foreign('group_id')->references('id')->on('snars_groups');
            $table->foreign('chapter_id')->references('id')->on('snars_chapters');
            $table->foreign('standard_id')->references('id')->on('snars_standards');
            $table->foreign('assigned_to')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            // Indexes
            $table->index('status');
            $table->index('scheduled_date');
            $table->index('frequency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_schedules');
    }
};
