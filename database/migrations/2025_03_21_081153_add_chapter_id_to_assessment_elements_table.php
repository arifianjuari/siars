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
        Schema::table('snars_assessment_elements', function (Blueprint $table) {
            // Periksa apakah kolom chapter_id sudah ada
            if (Schema::hasColumn('snars_assessment_elements', 'chapter_id')) {
                // Ubah kolom chapter_id menjadi nullable
                $table->uuid('chapter_id')->nullable()->change();
            } else {
                // Tambahkan kolom chapter_id sebagai nullable
                $table->uuid('chapter_id')->nullable()->after('standard_id');

                // Tambahkan foreign key ke snars_chapters
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
        Schema::table('snars_assessment_elements', function (Blueprint $table) {
            if (Schema::hasColumn('snars_assessment_elements', 'chapter_id')) {
                // Hapus foreign key jika ada
                $table->dropForeign(['chapter_id']);

                // Jika perlu, kembalikan kolom ke not nullable
                // $table->uuid('chapter_id')->nullable(false)->change();
            }
        });
    }
};
