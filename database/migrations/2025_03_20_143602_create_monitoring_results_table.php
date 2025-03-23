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
        Schema::create('monitoring_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('schedule_id')->comment('ID jadwal monitoring');
            $table->uuid('element_id')->nullable()->comment('ID elemen penilaian');
            $table->date('monitoring_date')->comment('Tanggal monitoring');
            $table->enum('status', ['compliant', 'partially_compliant', 'non_compliant'])
                  ->comment('Status kepatuhan');
            $table->text('findings')->nullable()->comment('Temuan monitoring');
            $table->text('action_taken')->nullable()->comment('Tindakan yang diambil');
            $table->text('recommendation')->nullable()->comment('Rekomendasi');
            $table->text('evidence')->nullable()->comment('Bukti monitoring');
            $table->unsignedBigInteger('monitored_by')->nullable()->comment('ID user yang melakukan monitoring');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('schedule_id')->references('id')->on('monitoring_schedules')->onDelete('cascade');
            $table->foreign('element_id')->references('id')->on('snars_assessment_elements');
            $table->foreign('monitored_by')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            // Indexes
            $table->index('status');
            $table->index('monitoring_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_results');
    }
};
