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
        Schema::create('assessment_periods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->comment('Nama periode penilaian');
            $table->date('start_date')->comment('Tanggal mulai periode');
            $table->date('end_date')->comment('Tanggal selesai periode');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])
                  ->default('planned')
                  ->comment('Status periode penilaian');
            $table->uuid('version_id')->comment('ID versi SNARS yang digunakan');
            $table->text('notes')->nullable()->comment('Catatan periode penilaian');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('version_id')->references('id')->on('snars_versions');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            // Indexes
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_periods');
    }
};
