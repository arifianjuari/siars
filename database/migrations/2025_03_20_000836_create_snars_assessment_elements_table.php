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
        Schema::create('snars_assessment_elements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('standard_id');
            $table->string('code', 20);
            $table->text('description');
            $table->text('assessment_guide')->nullable();
            $table->text('evidence_requirements')->nullable();
            $table->string('scoring_method', 50)->default('binary'); // binary, scale, etc.
            $table->integer('weight')->default(1);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->foreign('standard_id')->references('id')->on('snars_standards');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            $table->unique(['standard_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_assessment_elements');
    }
};
