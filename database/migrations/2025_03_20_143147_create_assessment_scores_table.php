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
        Schema::create('assessment_scores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('assessment_id')->comment('ID penilaian');
            $table->uuid('element_id')->comment('ID elemen penilaian');
            $table->enum('score', ['not_assessed', 'compliant', 'partially_compliant', 'non_compliant'])
                  ->default('not_assessed')
                  ->comment('Skor penilaian');
            $table->text('notes')->nullable()->comment('Catatan penilaian');
            $table->text('evidence')->nullable()->comment('Bukti penilaian');
            $table->text('recommendation')->nullable()->comment('Rekomendasi');
            $table->unsignedBigInteger('assessed_by')->nullable()->comment('ID user yang melakukan penilaian');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->foreign('element_id')->references('id')->on('snars_assessment_elements');
            $table->foreign('assessed_by')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            // Unique constraint untuk menghindari duplikasi penilaian
            $table->unique(['assessment_id', 'element_id'], 'unique_element_assessment');
            
            // Indexes
            $table->index('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_scores');
    }
};
