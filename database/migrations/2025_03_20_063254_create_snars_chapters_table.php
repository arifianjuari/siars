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
        Schema::create('snars_chapters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20)->comment('Kode bab');
            $table->string('name')->comment('Nama bab');
            $table->text('description')->nullable()->comment('Deskripsi bab');
            $table->integer('order')->default(0)->comment('Urutan bab dalam standar');
            $table->boolean('is_active')->default(true)->comment('Status aktif');
            $table->uuid('standard_id')->comment('ID standar SNARS');
            $table->foreign('standard_id')->references('id')->on('snars_standards')->onDelete('cascade');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->timestamps();
            
            // Composite unique key untuk kode bab dalam satu standar
            $table->unique(['code', 'standard_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_chapters');
    }
};
