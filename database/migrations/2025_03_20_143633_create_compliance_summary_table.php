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
        Schema::create('compliance_summary', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('period_id')->comment('ID periode penilaian');
            $table->uuid('entity_id')->comment('ID entitas (group, chapter, standard, element)');
            $table->enum('entity_type', ['group', 'chapter', 'standard', 'element'])
                  ->comment('Tipe entitas');
            $table->integer('total_elements')->default(0)->comment('Total elemen penilaian');
            $table->integer('compliant_count')->default(0)->comment('Jumlah elemen yang patuh');
            $table->integer('partially_compliant_count')->default(0)->comment('Jumlah elemen yang patuh sebagian');
            $table->integer('non_compliant_count')->default(0)->comment('Jumlah elemen yang tidak patuh');
            $table->integer('not_assessed_count')->default(0)->comment('Jumlah elemen yang belum dinilai');
            $table->decimal('compliance_percentage', 5, 2)->default(0)->comment('Persentase kepatuhan');
            $table->date('calculation_date')->comment('Tanggal perhitungan');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('period_id')->references('id')->on('assessment_periods');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            // Indexes
            $table->index(['entity_type', 'entity_id']);
            $table->index('calculation_date');
            
            // Unique constraint
            $table->unique(['period_id', 'entity_id', 'entity_type', 'calculation_date'], 'unique_compliance_summary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_summary');
    }
};
