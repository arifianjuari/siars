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
            // Menghapus kolom lama
            $table->dropColumn([
                'penyebab_langsung',
                'faktor_tim',
                'faktor_tim_lainnya',
                'faktor_sistem',
                'faktor_sistem_lainnya',
                'faktor_pasien',
                'faktor_pasien_lainnya',
                'faktor_lingkungan',
                'faktor_lingkungan_lainnya',
                'teknik_analisis',
                'teknik_lainnya',
                'rekomendasi_pendek',
                'rekomendasi_menengah',
                'rekomendasi_panjang',
                'analyzer_id'
            ]);

            // Menambahkan kolom baru
            $table->text('team_factors')->nullable()->after('incident_id');
            $table->text('system_factors')->nullable()->after('team_factors');
            $table->text('patient_factors')->nullable()->after('system_factors');
            $table->text('environmental_factors')->nullable()->after('patient_factors');
            $table->string('analysis_method')->after('environmental_factors');
            $table->text('root_causes')->after('analysis_method');
            $table->text('recommendations')->after('root_causes');
            $table->foreignId('analyzed_by')->constrained('users')->after('recommendations');
            $table->timestamp('analyzed_at')->nullable()->after('analyzed_by');
            $table->foreignId('updated_by')->nullable()->constrained('users')->after('analyzed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('root_cause_analyses', function (Blueprint $table) {
            // Menghapus kolom baru
            $table->dropColumn([
                'team_factors',
                'system_factors',
                'patient_factors',
                'environmental_factors',
                'analysis_method',
                'root_causes',
                'recommendations',
                'analyzed_by',
                'analyzed_at',
                'updated_by'
            ]);

            // Mengembalikan kolom lama
            $table->text('penyebab_langsung')->after('incident_id');
            $table->json('faktor_tim')->nullable()->after('penyebab_langsung');
            $table->text('faktor_tim_lainnya')->nullable()->after('faktor_tim');
            $table->json('faktor_sistem')->nullable()->after('faktor_tim_lainnya');
            $table->text('faktor_sistem_lainnya')->nullable()->after('faktor_sistem');
            $table->json('faktor_pasien')->nullable()->after('faktor_sistem_lainnya');
            $table->text('faktor_pasien_lainnya')->nullable()->after('faktor_pasien');
            $table->json('faktor_lingkungan')->nullable()->after('faktor_pasien_lainnya');
            $table->text('faktor_lingkungan_lainnya')->nullable()->after('faktor_lingkungan');
            $table->string('teknik_analisis')->after('faktor_lingkungan_lainnya');
            $table->string('teknik_lainnya')->nullable()->after('teknik_analisis');
            $table->text('rekomendasi_pendek')->after('teknik_lainnya');
            $table->text('rekomendasi_menengah')->nullable()->after('rekomendasi_pendek');
            $table->text('rekomendasi_panjang')->nullable()->after('rekomendasi_menengah');
            $table->foreignId('analyzer_id')->constrained('users')->after('rekomendasi_panjang');
        });
    }
};
