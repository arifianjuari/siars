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
        // Buat tabel risk_factors jika belum ada
        if (!Schema::hasTable('risk_factors')) {
            Schema::create('risk_factors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('report_id')->constrained('risk_reports')->onDelete('cascade');
                $table->string('factor_type'); // human, equipment, environment, etc.
                $table->text('description');
                $table->timestamps();
            });
        }

        // Buat tabel risk_mitigations jika belum ada
        if (!Schema::hasTable('risk_mitigations')) {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tabel jika ada
        Schema::dropIfExists('risk_factors');
        Schema::dropIfExists('risk_mitigations');
    }
};
