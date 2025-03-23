<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat fungsi observer/event untuk mencegah pengaktifan otomatis
     * modul SNARS untuk tenant baru di masa depan
     */
    public function up(): void
    {
        // Dokumentasi bahwa pengaktifan otomatis modul SNARS telah dihapus
        Log::info('Pengaktifan otomatis modul SNARS telah dinonaktifkan melalui migrasi');

        // Tidak perlu melakukan perubahan pada struktur database
        // Perubahan ini hanya dokumentasi untuk mencatat bahwa fitur pengaktifan
        // otomatis telah dihapus dari kode aplikasi

        // Catatan untuk developer: 
        // Pastikan ketika menambahkan tenant baru, TIDAK ada kode yang
        // secara otomatis mengaktifkan modul manapun termasuk SNARS
        // Semua modul harus melalui proses approval yang sama
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak ada efek rollback untuk migrasi ini karena hanya dokumentasi
        Log::info('Rollback migrasi pengaktifan otomatis modul SNARS');
    }
};
