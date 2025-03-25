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
        Schema::table('risk_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('risk_categories', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('risk_categories', function (Blueprint $table) {
            if (Schema::hasColumn('risk_categories', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
