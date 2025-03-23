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
        Schema::create('snars_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Kunci konfigurasi');
            $table->text('value')->nullable()->comment('Nilai konfigurasi');
            $table->string('type')->default('string')->comment('Tipe data nilai: string, integer, float, boolean, date, json');
            $table->text('description')->nullable()->comment('Deskripsi konfigurasi');
            $table->boolean('is_system')->default(false)->comment('Flag apakah konfigurasi sistem atau tidak');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });

        // Insert default survey date (1 Maret 2025)
        DB::table('snars_configurations')->insert([
            'key' => 'survey_date',
            'value' => '2025-03-01',
            'type' => 'date',
            'description' => 'Tanggal pelaksanaan survey akreditasi',
            'is_system' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_configurations');
    }
};
