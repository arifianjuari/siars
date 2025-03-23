<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            $table->string('title')->comment('Judul bab');
            $table->text('description')->nullable()->comment('Deskripsi bab');
            $table->integer('order')->default(0)->comment('Urutan bab');
            $table->boolean('is_active')->default(true)->comment('Status aktif');
            $table->uuid('group_id')->nullable()->comment('ID kelompok standar');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->timestamps();
            
            // Unique key untuk kode bab
            $table->unique('code');
        });
        
        // Insert sample data
        $chapters = [
            [
                'id' => Str::uuid()->toString(),
                'code' => 'APK',
                'title' => 'Asesmen Pasien dan Keluarga',
                'description' => 'Standar asesmen pasien dan keluarga',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => Str::uuid()->toString(),
                'code' => 'ARK',
                'title' => 'Akses ke Rumah Sakit dan Kontinuitas Pelayanan',
                'description' => 'Standar akses ke rumah sakit dan kontinuitas pelayanan',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => Str::uuid()->toString(),
                'code' => 'PAP',
                'title' => 'Pendidikan Pasien dan Keluarga',
                'description' => 'Standar pendidikan pasien dan keluarga',
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        
        DB::table('snars_chapters')->insert($chapters);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_chapters');
    }
};
