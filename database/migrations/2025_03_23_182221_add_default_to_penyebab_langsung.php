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
            // Mengubah kolom penyebab_langsung agar bisa null
            if (Schema::hasColumn('root_cause_analyses', 'penyebab_langsung')) {
                $table->text('penyebab_langsung')->nullable()->default(null)->change();
            }

            // Mengubah kolom teknik_analisis agar bisa null
            if (Schema::hasColumn('root_cause_analyses', 'teknik_analisis')) {
                $table->string('teknik_analisis')->nullable()->default(null)->change();
            }

            // Mengubah kolom analyzer_id agar bisa null
            if (Schema::hasColumn('root_cause_analyses', 'analyzer_id')) {
                $table->foreignId('analyzer_id')->nullable()->default(null)->change();
            }

            // Mengubah kolom rekomendasi_pendek agar bisa null
            if (Schema::hasColumn('root_cause_analyses', 'rekomendasi_pendek')) {
                $table->text('rekomendasi_pendek')->nullable()->default(null)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('root_cause_analyses', function (Blueprint $table) {
            // Mengembalikan kolom penyebab_langsung ke required (tidak nullable)
            if (Schema::hasColumn('root_cause_analyses', 'penyebab_langsung')) {
                $table->text('penyebab_langsung')->nullable(false)->change();
            }

            // Mengembalikan kolom teknik_analisis ke required (tidak nullable)
            if (Schema::hasColumn('root_cause_analyses', 'teknik_analisis')) {
                $table->string('teknik_analisis')->nullable(false)->change();
            }

            // Mengembalikan kolom analyzer_id ke required (tidak nullable)
            if (Schema::hasColumn('root_cause_analyses', 'analyzer_id')) {
                $table->foreignId('analyzer_id')->nullable(false)->change();
            }

            // Mengembalikan kolom rekomendasi_pendek ke required (tidak nullable)
            if (Schema::hasColumn('root_cause_analyses', 'rekomendasi_pendek')) {
                $table->text('rekomendasi_pendek')->nullable(false)->change();
            }
        });
    }
};
