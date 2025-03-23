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
        Schema::create('snars_notification_recipients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('notification_id')->comment('ID notifikasi');
            $table->unsignedBigInteger('user_id')->comment('ID user penerima');
            $table->boolean('is_read')->default(false)->comment('Status dibaca');
            $table->timestamp('read_at')->nullable()->comment('Waktu dibaca');
            $table->boolean('is_sent')->default(false)->comment('Status terkirim');
            $table->timestamp('sent_at')->nullable()->comment('Waktu terkirim');
            $table->enum('delivery_channel', ['app', 'email', 'sms', 'all'])
                  ->default('app')
                  ->comment('Saluran pengiriman');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('notification_id')->references('id')->on('snars_notifications')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
            
            // Indexes
            $table->index('is_read');
            $table->index('is_sent');
            
            // Unique constraint
            $table->unique(['notification_id', 'user_id'], 'unique_notification_recipient');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_notification_recipients');
    }
};
