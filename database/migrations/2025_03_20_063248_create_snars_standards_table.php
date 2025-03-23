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
        Schema::create('snars_standards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20)->comment('Kode standar');
            $table->string('name')->comment('Nama standar');
            $table->text('description')->nullable()->comment('Deskripsi standar');
            $table->text('purpose')->nullable()->comment('Tujuan standar');
            $table->integer('order')->default(0)->comment('Urutan standar dalam kelompok');
            $table->boolean('is_active')->default(true)->comment('Status aktif');
            $table->uuid('group_id')->comment('ID kelompok SNARS');
            $table->foreign('group_id')->references('id')->on('snars_groups')->onDelete('cascade');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->timestamps();
            
            // Composite unique key untuk kode standar dalam satu kelompok
            $table->unique(['code', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_standards');
    }
};
