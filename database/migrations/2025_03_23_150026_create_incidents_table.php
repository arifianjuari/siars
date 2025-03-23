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
        if (Schema::hasTable('incidents')) {
            return;
        }

        // Pastikan tabel locations sudah ada
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
