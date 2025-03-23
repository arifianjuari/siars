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
        Schema::create('snars_chapters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('snars_group_id')->constrained('snars_groups')->onDelete('cascade');
            $table->string('code', 50);
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('snars_group_id');
            $table->unique(['snars_group_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snars_chapters');
    }
};
