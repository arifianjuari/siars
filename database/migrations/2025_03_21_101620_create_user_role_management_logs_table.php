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
        Schema::create('user_role_management_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('target_user_id');
            $table->unsignedBigInteger('old_role_id')->nullable();
            $table->unsignedBigInteger('new_role_id');
            $table->uuid('tenant_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('users');
            $table->foreign('target_user_id')->references('id')->on('users');
            $table->foreign('old_role_id')->references('id')->on('roles');
            $table->foreign('new_role_id')->references('id')->on('roles');
            $table->foreign('tenant_id')->references('id')->on('tenants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_role_management_logs');
    }
};
