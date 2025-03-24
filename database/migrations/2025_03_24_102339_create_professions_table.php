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
        Schema::create('professions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default professions
        DB::table('professions')->insert([
            ['name' => 'Dokter Spesialis', 'code' => 'DS', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dokter Umum', 'code' => 'DU', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Perawat', 'code' => 'PR', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bidan', 'code' => 'BD', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Apoteker', 'code' => 'AP', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Asisten Apoteker', 'code' => 'AA', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Radiografer', 'code' => 'RD', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Analis Laboratorium', 'code' => 'AL', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fisioterapis', 'code' => 'FT', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ahli Gizi', 'code' => 'AG', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tenaga Administrasi', 'code' => 'TA', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professions');
    }
};
