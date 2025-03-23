<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menambahkan trigger untuk mengisi penyebab_langsung dari root_causes saat insert
        DB::unprepared('
            CREATE TRIGGER tr_root_cause_analyses_before_insert BEFORE INSERT ON `root_cause_analyses` 
            FOR EACH ROW 
            BEGIN
                IF NEW.penyebab_langsung IS NULL AND NEW.root_causes IS NOT NULL THEN 
                    SET NEW.penyebab_langsung = NEW.root_causes;
                END IF;
                
                IF NEW.teknik_analisis IS NULL AND NEW.analysis_method IS NOT NULL THEN 
                    SET NEW.teknik_analisis = NEW.analysis_method;
                END IF;
                
                IF NEW.rekomendasi_pendek IS NULL AND NEW.recommendations IS NOT NULL THEN 
                    SET NEW.rekomendasi_pendek = NEW.recommendations;
                END IF;
                
                IF NEW.analyzer_id IS NULL AND NEW.analyzed_by IS NOT NULL THEN 
                    SET NEW.analyzer_id = NEW.analyzed_by;
                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus trigger
        DB::unprepared('DROP TRIGGER IF EXISTS tr_root_cause_analyses_before_insert');
    }
};
