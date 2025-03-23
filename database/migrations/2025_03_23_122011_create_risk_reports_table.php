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
        Schema::create('risk_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique(); // Format: IKP-{TAHUN}-{NOMOR URUT}
            $table->string('title');
            $table->text('description');
            $table->foreignId('reporter_id')->constrained('users');
            $table->foreignId('department_id')->constrained('departments');
            $table->date('incident_date');
            $table->time('incident_time')->nullable();
            $table->foreignId('category_id')->constrained('risk_categories');
            $table->enum('status', ['reported', 'assessed', 'mitigated', 'closed'])->default('reported');
            $table->text('immediate_action')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_reports');
    }
};
