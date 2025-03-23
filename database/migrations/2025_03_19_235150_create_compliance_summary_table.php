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
            $table->uuid('tenant_id');
            $table->uuid('department_id')->nullable();
            $table->uuid('standard_id')->nullable();
            $table->uuid('element_id')->nullable();
            $table->date('period_start_date');
            $table->date('period_end_date');
            $table->integer('total_elements')->default(0);
            $table->integer('compliant_elements')->default(0);
            $table->integer('non_compliant_elements')->default(0);
            $table->integer('not_applicable_elements')->default(0);
            $table->decimal('compliance_percentage', 5, 2)->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('standard_id')->references('id')->on('snars_standards');
            $table->foreign('element_id')->references('id')->on('snars_assessment_elements');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
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
