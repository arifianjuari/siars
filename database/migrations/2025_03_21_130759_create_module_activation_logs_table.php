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
        Schema::create('module_activation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('request_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('action'); // activated, deactivated, requested, approved, rejected
            $table->text('notes')->nullable();
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamp('action_at');
            $table->timestamps();

            $table->foreign('module_id')->references('id')->on('modules');
            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('request_id')->references('id')->on('module_activation_requests')->nullOnDelete();

            // Indeks untuk performa
            $table->index(['tenant_id', 'module_id']);
            $table->index(['action_at']);
            $table->index(['action', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_activation_logs');
    }
};
