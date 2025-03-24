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
        Schema::table('incidents', function (Blueprint $table) {
            // Tambahkan kolom case_number jika belum ada
            if (!Schema::hasColumn('incidents', 'case_number')) {
                $table->string('case_number')->nullable()->unique()->after('id');
            }

            // Tambahkan kolom qr_code_path jika belum ada
            if (!Schema::hasColumn('incidents', 'qr_code_path')) {
                $table->string('qr_code_path')->nullable()->after('dokumen_evaluasi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            // Hapus kolom case_number dan qr_code_path jika ada
            if (Schema::hasColumn('incidents', 'case_number')) {
                $table->dropColumn('case_number');
            }

            if (Schema::hasColumn('incidents', 'qr_code_path')) {
                $table->dropColumn('qr_code_path');
            }
        });
    }
};
