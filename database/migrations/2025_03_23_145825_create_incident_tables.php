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
        // Tabel untuk menyimpan jenis-jenis insiden
        if (!Schema::hasTable('incident_types')) {
            Schema::create('incident_types', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Nama jenis insiden (mis: Medication Error, Patient Fall, dll)
                $table->string('code')->nullable(); // Kode jenis insiden (opsional)
                $table->text('description')->nullable(); // Deskripsi dari jenis insiden
                $table->boolean('is_active')->default(true); // Status aktif
                $table->timestamps();
            });
        }

        // Tabel untuk menyimpan sub-jenis insiden
        if (!Schema::hasTable('incident_subtypes')) {
            Schema::create('incident_subtypes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('incident_type_id')->constrained()->onDelete('cascade');
                $table->string('name'); // Nama sub-jenis insiden
                $table->text('description')->nullable(); // Deskripsi sub-jenis
                $table->boolean('is_active')->default(true); // Status aktif
                $table->timestamps();
            });
        }

        // Tabel untuk menyimpan lokasi insiden
        if (!Schema::hasTable('locations')) {
            Schema::create('locations', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Nama lokasi (mis: Ruang Rawat, ICU, Laboratorium, dll)
                $table->string('code')->nullable(); // Kode lokasi
                $table->text('description')->nullable(); // Deskripsi lokasi
                $table->boolean('is_active')->default(true); // Status aktif
                $table->timestamps();
            });
        }

        // Tabel utama untuk insiden
        if (!Schema::hasTable('incidents')) {
            Schema::create('incidents', function (Blueprint $table) {
                $table->id();
                $table->dateTime('tanggal_waktu_kejadian'); // Tanggal dan waktu insiden terjadi
                $table->foreignId('location_id')->constrained(); // Lokasi kejadian
                $table->foreignId('incident_type_id')->constrained(); // Jenis insiden
                $table->foreignId('incident_subtype_id')->nullable()->constrained(); // Sub-jenis insiden (opsional)
                $table->string('nama_pasien'); // Nama pasien
                $table->string('no_rm'); // Nomor rekam medis
                $table->text('kronologis'); // Kronologis kejadian
                $table->foreignId('reporter_id')->constrained('users'); // ID pelapor (merujuk ke tabel users)
                $table->string('status')->default('Baru'); // Status: Baru, Proses, Evaluasi, Selesai
                $table->dateTime('tanggal_evaluasi')->nullable(); // Tanggal evaluasi
                $table->text('detail_penanganan')->nullable(); // Detail penanganan insiden
                $table->text('hasil_monitoring')->nullable(); // Hasil monitoring
                $table->string('status_penutupan')->nullable(); // Status penutupan: Diselesaikan, Tidak Diselesaikan, Monitoring Berkelanjutan
                $table->text('catatan_tambahan')->nullable(); // Catatan tambahan
                $table->string('dokumen_pendukung')->nullable(); // Path ke dokumen pendukung
                $table->string('dokumen_evaluasi')->nullable(); // Path ke dokumen evaluasi
                $table->timestamps();
                $table->softDeletes(); // Soft delete
            });
        }

        // Tabel untuk menyimpan klasifikasi risiko
        if (!Schema::hasTable('risk_classifications')) {
            Schema::create('risk_classifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('incident_id')->constrained()->onDelete('cascade');
                $table->integer('dampak'); // Nilai dampak (1-5)
                $table->string('dampak_detail'); // Detail dampak
                $table->integer('probabilitas'); // Nilai probabilitas (1-5)
                $table->string('probabilitas_detail'); // Detail probabilitas
                $table->integer('skor_risiko'); // Skor risiko (dampak x probabilitas)
                $table->string('level_risiko'); // Level risiko (Rendah, Sedang, Tinggi, Ekstrim)
                $table->string('zona_risiko'); // Zona risiko (Hijau, Kuning, Merah)
                $table->foreignId('classifier_id')->constrained('users'); // User yang melakukan klasifikasi
                $table->timestamps();
            });
        }

        // Tabel untuk menyimpan analisis akar masalah
        if (!Schema::hasTable('root_cause_analyses')) {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('root_cause_analyses');
        Schema::dropIfExists('risk_classifications');
        Schema::dropIfExists('incidents');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('incident_subtypes');
        Schema::dropIfExists('incident_types');
    }
};
