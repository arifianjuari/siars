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
        Schema::create('snars_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->comment('Judul notifikasi');
            $table->text('message')->comment('Isi notifikasi');
            $table->enum('type', ['info', 'warning', 'deadline', 'update', 'other'])
                  ->default('info')
                  ->comment('Tipe notifikasi');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                  ->default('medium')
                  ->comment('Prioritas notifikasi');
            $table->uuid('related_entity_id')->nullable()->comment('ID entitas terkait');
            $table->string('related_entity_type')->nullable()->comment('Tipe entitas terkait');
            $table->timestamp('scheduled_at')->nullable()->comment('Waktu notifikasi dijadwalkan');
            $table->timestamp('expires_at')->nullable()->comment('Waktu notifikasi kadaluarsa');
            $table->boolean('is_system_generated')->default(false)->comment('Apakah dibuat oleh sistem');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            // Indexes
            $table->index('type');
            $table->index('priority');
            $table->index('scheduled_at');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_notifications');
    }
};
