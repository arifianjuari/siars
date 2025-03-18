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
        Schema::create('elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_id')->constrained()->onDelete('cascade');
            $table->string('code', 20);
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('assessment_guidelines')->nullable();
            $table->text('evidence_requirements')->nullable();
            $table->enum('type', ['EP', 'DKP', 'AP', 'TKRS'])->comment('EP: Elemen Penilaian, DKP: Dokumen Kebijakan dan Prosedur, AP: Acuan Penilaian, TKRS: Telusur Keselamatan Rumah Sakit');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elements');
    }
};
