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
        Schema::table('snars_standards', function (Blueprint $table) {
            // Periksa apakah kolom chapter_id sudah ada
            if (!Schema::hasColumn('snars_standards', 'chapter_id')) {
                // Tambahkan kolom chapter_id
                $table->uuid('chapter_id')->nullable()->after('id');

                // Tambahkan foreign key
                $table->foreign('chapter_id')
                    ->references('id')
                    ->on('snars_chapters')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('snars_standards', function (Blueprint $table) {
            // Hapus foreign key jika ada
            if (Schema::hasColumn('snars_standards', 'chapter_id')) {
                $table->dropForeign(['chapter_id']);
                $table->dropColumn('chapter_id');
            }
        });
    }
};
