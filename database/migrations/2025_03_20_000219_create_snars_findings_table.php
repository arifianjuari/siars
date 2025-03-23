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
        Schema::create('snars_findings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('assessment_score_id');
            $table->string('finding_type', 50); // Non-conformity, Observation, Opportunity for Improvement
            $table->text('description');
            $table->text('root_cause_analysis')->nullable();
            $table->text('corrective_action')->nullable();
            $table->date('target_completion_date')->nullable();
            $table->string('status', 50); // Open, In Progress, Closed
            $table->unsignedBigInteger('responsible_person')->nullable();
            $table->text('evidence')->nullable();
            $table->date('closed_date')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->foreign('assessment_score_id')->references('id')->on('snars_assessment_scores');
            $table->foreign('responsible_person')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_findings');
    }
};
