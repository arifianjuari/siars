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
        Schema::table('snars_assessment_elements', function (Blueprint $table) {
            // Periksa apakah kolom evidence_requirements sudah ada
            if (!Schema::hasColumn('snars_assessment_elements', 'evidence_requirements')) {
                $table->text('evidence_requirements')->nullable()->after('assessment_guide');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('snars_assessment_elements', function (Blueprint $table) {
            if (Schema::hasColumn('snars_assessment_elements', 'evidence_requirements')) {
                $table->dropColumn('evidence_requirements');
            }
        });
    }
};
