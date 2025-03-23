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
        if (Schema::hasTable('risk_classifications')) {
            return;
        }

        Schema::create('risk_classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->onDelete('cascade');
            $table->integer('dampak'); // Nilai dampak (1-5)
            $table->string('dampak_detail')->nullable(); // Detail dampak
            $table->integer('probabilitas'); // Nilai probabilitas (1-5)
            $table->string('probabilitas_detail')->nullable(); // Detail probabilitas
            $table->integer('skor_risiko'); // Skor risiko (dampak x probabilitas)
            $table->string('level_risiko'); // Level risiko (Rendah, Sedang, Tinggi, Ekstrim)
            $table->string('zona_risiko'); // Zona risiko (Hijau, Kuning, Merah)
            $table->foreignId('classifier_id')->constrained('users'); // User yang melakukan klasifikasi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_classifications');
    }
};
