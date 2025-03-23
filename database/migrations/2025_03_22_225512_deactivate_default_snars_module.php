<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menonaktifkan modul SNARS yang sudah aktif secara default
     * agar memiliki perlakuan yang sama dengan modul lainnya
     */
    public function up(): void
    {
        // Nonaktifkan modul SNARS yang sudah aktif secara default
        DB::table('tenant_modules')
            ->where('module_id', 1) // Modul SNARS (ID: 1)
            ->update([
                'is_active' => false,
                'approved_by' => null,
                'approved_at' => null
            ]);
    }

    /**
     * Reverse the migrations.
     * Mengembalikan status aktif jika diperlukan
     */
    public function down(): void
    {
        // Kembalikan status aktif
        DB::table('tenant_modules')
            ->where('module_id', 1) // Modul SNARS (ID: 1)
            ->update([
                'is_active' => true,
                'approved_at' => now()
            ]);
    }
};
