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
            // Tambahkan kolom analyzed_by, analyzed_at, dan updated_by jika belum ada
            if (!Schema::hasColumn('root_cause_analyses', 'analyzed_by')) {
                $table->foreignId('analyzed_by')->nullable()->after('recommendations');
            }

            if (!Schema::hasColumn('root_cause_analyses', 'analyzed_at')) {
                $table->timestamp('analyzed_at')->nullable()->after('analyzed_by');
            }

            if (!Schema::hasColumn('root_cause_analyses', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('analyzed_at');
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
            if (Schema::hasColumn('root_cause_analyses', 'analyzed_by')) {
                $table->dropColumn('analyzed_by');
            }

            if (Schema::hasColumn('root_cause_analyses', 'analyzed_at')) {
                $table->dropColumn('analyzed_at');
            }

            if (Schema::hasColumn('root_cause_analyses', 'updated_by')) {
                $table->dropColumn('updated_by');
            }
        });
    }
};
