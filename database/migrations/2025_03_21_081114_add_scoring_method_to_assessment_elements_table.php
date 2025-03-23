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
            // Periksa apakah kolom scoring_method sudah ada
            if (!Schema::hasColumn('snars_assessment_elements', 'scoring_method')) {
                $table->string('scoring_method', 50)->default('binary')->after('evidence_requirements');
            }

            // Jika kolom weight tidak ada, tambahkan juga
            if (!Schema::hasColumn('snars_assessment_elements', 'weight')) {
                $table->integer('weight')->default(1)->after('scoring_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('snars_assessment_elements', function (Blueprint $table) {
            if (Schema::hasColumn('snars_assessment_elements', 'scoring_method')) {
                $table->dropColumn('scoring_method');
            }

            if (Schema::hasColumn('snars_assessment_elements', 'weight')) {
                $table->dropColumn('weight');
            }
        });
    }
};
