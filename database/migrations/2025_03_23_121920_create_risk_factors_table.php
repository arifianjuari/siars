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
        Schema::create('risk_factors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('risk_reports')->onDelete('cascade');
            $table->string('factor_type'); // human, equipment, environment, etc.
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_factors');
    }
};
