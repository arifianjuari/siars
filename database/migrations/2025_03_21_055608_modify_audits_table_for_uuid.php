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
        $connection = config('audit.drivers.database.connection', config('database.default'));
        $table = config('audit.drivers.database.table', 'audits');

        Schema::connection($connection)->table($table, function (Blueprint $table) {
            // Ubah indeks terlebih dahulu untuk menghindari konflik
            $table->dropIndex(['auditable_type', 'auditable_id']);

            // Mengubah tipe data kolom auditable_id
            $table->string('auditable_id', 36)->change();

            // Membuat indeks kembali
            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = config('audit.drivers.database.connection', config('database.default'));
        $table = config('audit.drivers.database.table', 'audits');

        Schema::connection($connection)->table($table, function (Blueprint $table) {
            // Ubah indeks terlebih dahulu untuk menghindari konflik
            $table->dropIndex(['auditable_type', 'auditable_id']);

            // Ubah kembali ke unsignedBigInteger
            $table->unsignedBigInteger('auditable_id')->change();

            // Membuat indeks kembali
            $table->index(['auditable_type', 'auditable_id']);
        });
    }
};
