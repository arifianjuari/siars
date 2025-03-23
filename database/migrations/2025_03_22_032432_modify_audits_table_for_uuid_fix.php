<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\StringType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mendaftarkan tipe untuk doctrine/dbal
        if (!Type::hasType('string')) {
            Type::addType('string', StringType::class);
        }

        Schema::table('audits', function (Blueprint $table) {
            // Ubah tipe data kolom auditable_id
            $table->string('auditable_id', 36)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            // Kembalikan ke tipe data sebelumnya jika diperlukan
            $table->unsignedBigInteger('auditable_id')->change();
        });
    }
};
