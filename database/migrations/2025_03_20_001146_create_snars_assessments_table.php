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
        Schema::create('snars_assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('period_id');
            $table->foreignId('department_id')->constrained('departments');
            $table->date('assessment_date');
            $table->string('status', 50)->default('planned'); // planned, in-progress, completed, cancelled
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('assessor_id')->nullable(); // User who conducted the assessment
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->foreign('period_id')->references('id')->on('snars_assessment_periods');
            $table->foreign('assessor_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_assessments');
    }
};
