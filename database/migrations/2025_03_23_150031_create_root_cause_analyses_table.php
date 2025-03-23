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
        // Skip jika sudah ada dari migrasi create_incident_tables
        if (Schema::hasTable('root_cause_analyses')) {
            return;
        }

        Schema::create('root_cause_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->onDelete('cascade');
            $table->text('penyebab_langsung'); // Penyebab langsung
            $table->json('faktor_tim')->nullable(); // Faktor kontributor dari tim
            $table->text('faktor_tim_lainnya')->nullable(); // Faktor tim lainnya
            $table->json('faktor_sistem')->nullable(); // Faktor kontributor dari sistem
            $table->text('faktor_sistem_lainnya')->nullable(); // Faktor sistem lainnya
            $table->json('faktor_pasien')->nullable(); // Faktor kontributor dari pasien
            $table->text('faktor_pasien_lainnya')->nullable(); // Faktor pasien lainnya
            $table->json('faktor_lingkungan')->nullable(); // Faktor kontributor dari lingkungan
            $table->text('faktor_lingkungan_lainnya')->nullable(); // Faktor lingkungan lainnya
            $table->string('teknik_analisis'); // Teknik analisis yang digunakan
            $table->string('teknik_lainnya')->nullable(); // Teknik lainnya jika dipilih 'Lainnya'
            $table->text('rekomendasi_pendek'); // Rekomendasi jangka pendek
            $table->text('rekomendasi_menengah')->nullable(); // Rekomendasi jangka menengah
            $table->text('rekomendasi_panjang')->nullable(); // Rekomendasi jangka panjang
            $table->foreignId('analyzer_id')->constrained('users'); // User yang melakukan analisis
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('root_cause_analyses');
    }
};
