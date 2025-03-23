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
        Schema::create('risk_mitigations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('risk_reports')->onDelete('cascade');
            $table->text('action_description');
            $table->foreignId('responsible_id')->constrained('users');
            $table->date('target_date');
            $table->enum('status', ['planned', 'in_progress', 'completed'])->default('planned');
            $table->date('completion_date')->nullable();
            $table->enum('effectiveness', ['effective', 'partially', 'not_effective'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_mitigations');
    }
};
