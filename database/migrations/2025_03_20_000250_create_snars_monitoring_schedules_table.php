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
        Schema::create('snars_monitoring_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('element_id')->constrained('elements');
            $table->string('frequency', 50); // Daily, Weekly, Monthly, Quarterly, Yearly
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('responsible_person')->nullable();
            $table->unsignedBigInteger('head_of_department')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->foreign('responsible_person')->references('id')->on('users');
            $table->foreign('head_of_department')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_monitoring_schedules');
    }
};
