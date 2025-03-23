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
            $table->string('element_code', 20);
            $table->text('element_description');
            $table->string('scoring_method', 50)->nullable(); // misal: 0-10, Ya/Tidak, dll
            $table->decimal('weight', 5, 2)->default(1.0);
            $table->boolean('is_critical')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order_number');
            $table->string('version', 50);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->foreign('standard_id')->references('id')->on('snars_standards');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            $table->unique(['standard_id', 'element_code', 'version']);
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
