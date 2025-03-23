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
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->comment('Judul widget');
            $table->string('widget_type')->comment('Tipe widget (chart, table, summary, etc)');
            $table->string('chart_type')->nullable()->comment('Tipe chart jika widget adalah chart');
            $table->text('configuration')->nullable()->comment('Konfigurasi widget dalam format JSON');
            $table->text('query_params')->nullable()->comment('Parameter query dalam format JSON');
            $table->integer('position')->default(0)->comment('Posisi widget pada dashboard');
            $table->integer('width')->default(12)->comment('Lebar widget (1-12 untuk grid 12 kolom)');
            $table->integer('height')->default(1)->comment('Tinggi widget dalam satuan baris');
            $table->boolean('is_active')->default(true)->comment('Status aktif widget');
            $table->unsignedBigInteger('user_id')->nullable()->comment('ID user jika widget adalah personal');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            // Indexes
            $table->index('widget_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_widgets');
    }
};
