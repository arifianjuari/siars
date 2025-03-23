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
        Schema::table('root_cause_analyses', function (Blueprint $table) {
            $table->text('team_factors')->nullable()->after('incident_id');
            $table->text('system_factors')->nullable()->after('team_factors');
            $table->text('patient_factors')->nullable()->after('system_factors');
            $table->text('environmental_factors')->nullable()->after('patient_factors');
            $table->string('analysis_method')->nullable()->after('environmental_factors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('root_cause_analyses', function (Blueprint $table) {
            $table->dropColumn([
                'team_factors',
                'system_factors',
                'patient_factors',
                'environmental_factors',
                'analysis_method'
            ]);
        });
    }
};
