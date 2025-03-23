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
        Schema::create('snars_assessment_scores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('assessment_id')->constrained('assessments');
            $table->foreignId('element_id')->constrained('elements');
            $table->decimal('score', 5, 2)->nullable();
            $table->boolean('is_compliant')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('assessed_by')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->foreign('assessed_by')->references('id')->on('users');
            $table->foreign('verified_by')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            $table->unique(['assessment_id', 'element_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_assessment_scores');
    }
};
