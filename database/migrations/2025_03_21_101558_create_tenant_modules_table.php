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
        Schema::create('tenant_modules', function (Blueprint $table) {
            $table->uuid('tenant_id');
            $table->unsignedBigInteger('module_id');
            $table->boolean('is_active')->default(false);
            $table->json('settings')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->primary(['tenant_id', 'module_id']);

            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->foreign('module_id')->references('id')->on('modules');
            $table->foreign('approved_by')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });

        // Aktifkan modul SNARS untuk semua tenant yang ada (tetap aktif secara default)
        DB::statement('
            INSERT INTO tenant_modules (tenant_id, module_id, is_active, approved_at, created_at, updated_at)
            SELECT id, 1, TRUE, NOW(), NOW(), NOW() FROM tenants
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_modules');
    }
};
