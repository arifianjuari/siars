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
        // Gunakan DB statement untuk rename kolom karena lebih aman
        DB::statement('ALTER TABLE snars_chapters CHANGE name title VARCHAR(255) NOT NULL');
        
        // Tambahkan foreign key constraint jika belum ada
        if (!Schema::hasForeignKey('snars_chapters', 'snars_chapters_snars_group_id_foreign')) {
            Schema::table('snars_chapters', function (Blueprint $table) {
                $table->foreign('snars_group_id')->references('id')->on('snars_groups')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Gunakan DB statement untuk rename kolom karena lebih aman
        DB::statement('ALTER TABLE snars_chapters CHANGE title name VARCHAR(255) NOT NULL');
    }
};
