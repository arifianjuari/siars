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
            // Tambahkan kolom root_causes dan recommendations jika belum ada
            if (!Schema::hasColumn('root_cause_analyses', 'root_causes')) {
                $table->text('root_causes')->after('analysis_method');
            }

            if (!Schema::hasColumn('root_cause_analyses', 'recommendations')) {
                $table->text('recommendations')->after('root_causes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('root_cause_analyses', function (Blueprint $table) {
            // Hapus kolom jika ada
            if (Schema::hasColumn('root_cause_analyses', 'root_causes')) {
                $table->dropColumn('root_causes');
            }

            if (Schema::hasColumn('root_cause_analyses', 'recommendations')) {
                $table->dropColumn('recommendations');
            }
        });
    }
};
