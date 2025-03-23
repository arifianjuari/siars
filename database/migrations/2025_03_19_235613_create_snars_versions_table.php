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
        Schema::create('snars_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('version_number', 50);
            $table->string('name', 100);
            $table->date('release_date');
            $table->date('effective_date');
            $table->text('description')->nullable();
            $table->text('change_summary')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            $table->unique('version_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_versions');
    }
};
