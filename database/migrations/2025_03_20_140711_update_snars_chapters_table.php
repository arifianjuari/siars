<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Langkah 1: Tambahkan kolom snars_group_id tanpa constraint dulu
        Schema::table('snars_chapters', function (Blueprint $table) {
            $table->uuid('snars_group_id')->after('id')->nullable();
        });
        
        // Langkah 2: Update data yang ada dengan default group_id
        DB::statement("UPDATE snars_chapters SET snars_group_id = (SELECT id FROM snars_groups LIMIT 1)");
        
        // Langkah 3: Tambahkan constraint dan indeks setelah data diisi
        Schema::table('snars_chapters', function (Blueprint $table) {
            // Ubah kolom menjadi tidak nullable
            $table->uuid('snars_group_id')->nullable(false)->change();
            
            // Tambahkan foreign key constraint
            $table->foreign('snars_group_id')->references('id')->on('snars_groups')->onDelete('cascade');
            
            // Rename kolom name menjadi title jika kolom name ada
            if (Schema::hasColumn('snars_chapters', 'name')) {
                $table->renameColumn('name', 'title');
            }
            
            // Hapus kolom standard_id jika ada
            if (Schema::hasColumn('snars_chapters', 'standard_id')) {
                $table->dropColumn('standard_id');
            }
            
            // Tambah indeks
            $table->index('snars_group_id');
            $table->unique(['snars_group_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('snars_chapters', function (Blueprint $table) {
            // Hapus indeks jika ada
            if (Schema::hasIndex('snars_chapters', 'snars_chapters_snars_group_id_index')) {
                $table->dropIndex(['snars_group_id']);
            }
            if (Schema::hasIndex('snars_chapters', 'snars_chapters_snars_group_id_code_unique')) {
                $table->dropUnique(['snars_group_id', 'code']);
            }
            
            // Hapus kolom yang ditambahkan
            if (Schema::hasColumn('snars_chapters', 'snars_group_id')) {
                $table->dropForeign(['snars_group_id']);
                $table->dropColumn('snars_group_id');
            }
            
            // Kembalikan nama kolom jika title ada
            if (Schema::hasColumn('snars_chapters', 'title')) {
                $table->renameColumn('title', 'name');
            }
            
            // Tambahkan kembali kolom standard_id jika tidak ada
            if (!Schema::hasColumn('snars_chapters', 'standard_id')) {
                $table->uuid('standard_id')->nullable();
            }
        });
    }
};
