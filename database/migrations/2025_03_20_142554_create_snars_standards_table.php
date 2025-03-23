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
            $table->uuid('chapter_id')->comment('ID bab SNARS');
            $table->string('code', 20)->comment('Kode standar');
            $table->string('title')->comment('Judul standar');
            $table->text('description')->nullable()->comment('Deskripsi standar');
            $table->text('purpose')->nullable()->comment('Maksud dan tujuan standar');
            $table->integer('order')->default(0)->comment('Urutan standar dalam bab');
            $table->boolean('is_active')->default(true)->comment('Status aktif');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('chapter_id')->references('id')->on('snars_chapters')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            // Unique constraints
            $table->unique(['code', 'chapter_id']);
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
