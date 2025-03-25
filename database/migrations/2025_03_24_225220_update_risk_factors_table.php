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
        Schema::table('risk_factors', function (Blueprint $table) {
            // Tambahkan kolom baru jika belum ada
            if (!Schema::hasColumn('risk_factors', 'code')) {
                $table->string('code')->nullable()->after('id');
            }

            if (!Schema::hasColumn('risk_factors', 'name')) {
                $table->string('name')->nullable()->after('code');
            }

            if (!Schema::hasColumn('risk_factors', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }

            if (!Schema::hasColumn('risk_factors', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('is_active');
            }

            // Buat kolom report_id dan factor_type menjadi nullable jika belum
            if (Schema::hasColumn('risk_factors', 'report_id')) {
                $table->unsignedBigInteger('report_id')->nullable()->change();
            }

            if (Schema::hasColumn('risk_factors', 'factor_type')) {
                $table->string('factor_type')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('risk_factors', function (Blueprint $table) {
            // Hapus kolom-kolom yang ditambahkan jika ada
            $columns = [];

            if (Schema::hasColumn('risk_factors', 'code')) {
                $columns[] = 'code';
            }

            if (Schema::hasColumn('risk_factors', 'name')) {
                $columns[] = 'name';
            }

            if (Schema::hasColumn('risk_factors', 'is_active')) {
                $columns[] = 'is_active';
            }

            if (Schema::hasColumn('risk_factors', 'tenant_id')) {
                $columns[] = 'tenant_id';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }

            // Kembalikan kolom report_id dan factor_type menjadi not nullable jika ada
            if (Schema::hasColumn('risk_factors', 'report_id')) {
                $table->unsignedBigInteger('report_id')->nullable(false)->change();
            }

            if (Schema::hasColumn('risk_factors', 'factor_type')) {
                $table->string('factor_type')->nullable(false)->change();
            }
        });
    }
};
