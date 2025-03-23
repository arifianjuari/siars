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
        Schema::create('snars_updates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('version_id');
            $table->string('update_type', 50); // Major, Minor, Patch
            $table->string('title', 100);
            $table->text('description');
            $table->text('changes');
            $table->date('update_date');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->foreign('version_id')->references('id')->on('snars_versions');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_updates');
    }
};
