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
        Schema::create('snars_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20)->unique()->comment('Kode kelompok');
            $table->string('name')->comment('Nama kelompok');
            $table->text('description')->nullable()->comment('Deskripsi kelompok');
            $table->integer('order')->default(0)->comment('Urutan kelompok');
            $table->boolean('is_active')->default(true)->comment('Status aktif');
            $table->uuid('version_id')->comment('ID versi SNARS');
            $table->foreign('version_id')->references('id')->on('snars_versions');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_groups');
    }
};
