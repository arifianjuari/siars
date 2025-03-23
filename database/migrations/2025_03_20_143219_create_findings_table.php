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
        Schema::create('findings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('assessment_id')->comment('ID penilaian');
            $table->uuid('element_id')->nullable()->comment('ID elemen penilaian');
            $table->string('title')->comment('Judul temuan');
            $table->text('description')->comment('Deskripsi temuan');
            $table->enum('severity', ['minor', 'major', 'critical'])
                  ->default('minor')
                  ->comment('Tingkat keparahan temuan');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])
                  ->default('open')
                  ->comment('Status temuan');
            $table->text('action_plan')->nullable()->comment('Rencana tindak lanjut');
            $table->date('due_date')->nullable()->comment('Tanggal tenggat waktu');
            $table->date('resolved_date')->nullable()->comment('Tanggal penyelesaian');
            $table->text('resolution')->nullable()->comment('Penyelesaian temuan');
            $table->unsignedBigInteger('assigned_to')->nullable()->comment('ID user yang ditugaskan');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->foreign('element_id')->references('id')->on('snars_assessment_elements');
            $table->foreign('assigned_to')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            // Indexes
            $table->index('status');
            $table->index('severity');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('findings');
    }
};
