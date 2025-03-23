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
        // Pastikan struktur tabel risk_reports sesuai dengan kebutuhan
        if (!Schema::hasTable('risk_reports')) {
            Schema::create('risk_reports', function (Blueprint $table) {
                $table->id();
                $table->string('report_number')->unique(); // Format: IKP-{TAHUN}-{NOMOR URUT}
                $table->string('title');
                $table->text('description');
                $table->foreignId('reporter_id')->constrained('users');
                $table->foreignId('department_id')->constrained('departments');
                $table->date('incident_date');
                $table->time('incident_time')->nullable();
                $table->foreignId('category_id')->constrained('risk_categories');
                $table->enum('status', ['reported', 'assessed', 'mitigated', 'closed'])->default('reported');
                $table->text('immediate_action')->nullable();
                $table->timestamps();
            });
        } else {
            // Tambahkan kolom yang diperlukan jika belum ada
            if (!Schema::hasColumn('risk_reports', 'report_number')) {
                Schema::table('risk_reports', function (Blueprint $table) {
                    $table->string('report_number')->unique()->after('id'); // Format: IKP-{TAHUN}-{NOMOR URUT}
                });
            }

            if (!Schema::hasColumn('risk_reports', 'immediate_action')) {
                Schema::table('risk_reports', function (Blueprint $table) {
                    $table->text('immediate_action')->nullable()->after('status');
                });
            }

            if (!Schema::hasColumn('risk_reports', 'status')) {
                Schema::table('risk_reports', function (Blueprint $table) {
                    $table->enum('status', ['reported', 'assessed', 'mitigated', 'closed'])->default('reported')->after('category_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak melakukan rollback karena ini hanya penyesuaian
    }
};
