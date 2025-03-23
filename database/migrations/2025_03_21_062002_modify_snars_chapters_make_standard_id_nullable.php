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
        Schema::table('snars_chapters', function (Blueprint $table) {
            // Cek apakah kolom standard_id ada, jika ada, ubah menjadi nullable
            if (Schema::hasColumn('snars_chapters', 'standard_id')) {
                $table->uuid('standard_id')->nullable()->change();
            } else {
                // Jika kolom tidak ada, tambahkan sebagai nullable
                $table->uuid('standard_id')->nullable()->after('snars_group_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('snars_chapters', function (Blueprint $table) {
            // Rollback: Kembalikan kolom ke wajib diisi jika sebelumnya sudah ada
            if (Schema::hasColumn('snars_chapters', 'standard_id')) {
                $table->uuid('standard_id')->nullable(false)->change();
            }
        });
    }
};
