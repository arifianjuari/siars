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
            // Periksa apakah kolom standard_id sudah ada
            if (!Schema::hasColumn('snars_assessment_elements', 'standard_id')) {
                // Tambahkan kolom standard_id
                $table->uuid('standard_id')->nullable()->after('id');

                // Tambahkan foreign key ke snars_standards
                $table->foreign('standard_id')
                    ->references('id')
                    ->on('snars_standards')
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
            // Hapus foreign key jika ada
            if (Schema::hasColumn('snars_assessment_elements', 'standard_id')) {
                $table->dropForeign(['standard_id']);
                $table->dropColumn('standard_id');
            }
        });
    }
};
