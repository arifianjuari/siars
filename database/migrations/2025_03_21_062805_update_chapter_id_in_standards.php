<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SnarsStandard;
use App\Models\SnarsChapter;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mendapatkan semua standard yang chapter_id-nya NULL
        $standards = DB::table('snars_standards')
            ->whereNull('chapter_id')
            ->get();

        foreach ($standards as $standard) {
            // Coba cari chapter yang cocok berdasarkan hubungan yang ada
            // (mungkin berdasarkan group_id, code, atau informasi lainnya)
            $chapter = DB::table('snars_chapters')
                ->where('snars_group_id', $standard->group_id)
                ->first();

            if ($chapter) {
                // Update chapter_id jika chapter ditemukan
                DB::table('snars_standards')
                    ->where('id', $standard->id)
                    ->update(['chapter_id' => $chapter->id]);

                echo "Updated standard ID: {$standard->id} with chapter ID: {$chapter->id}\n";
            } else {
                echo "Could not find matching chapter for standard ID: {$standard->id}\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak ada operasi yang perlu di-reverse karena ini hanya update data
    }
};
