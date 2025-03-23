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
            $table->uuid('tenant_id');
            $table->string('title', 255);
            $table->text('content');
            $table->string('notification_type', 50); // System, Assessment, Compliance, Update
            $table->string('priority', 20)->default('normal'); // low, normal, high, urgent
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->string('source_type', 50)->nullable(); // The type of entity that triggered the notification
            $table->string('source_id', 36)->nullable(); // UUID of the source entity
            $table->string('action_url')->nullable(); // URL to direct user when clicking the notification
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            
            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->foreign('created_by')->references('id')->on('users');
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
