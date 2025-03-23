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
        // Tabel Kategori Risiko
        if (!Schema::hasTable('risk_categories')) {
            Schema::create('risk_categories', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('tenant_id');
                $table->string('name', 100);
                $table->string('code', 20)->nullable();
                $table->text('description')->nullable();
                $table->uuid('parent_id')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->json('metadata')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('parent_id')->references('id')->on('risk_categories')->onUpdate('cascade')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            });
        }

        // Tabel Registrasi Risiko (induk)
        if (!Schema::hasTable('risk_registry')) {
            Schema::create('risk_registry', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('tenant_id');
                $table->string('risk_code', 50);
                $table->string('title', 200);
                $table->text('description')->nullable();
                $table->uuid('category_id');
                $table->uuid('subcategory_id')->nullable();
                $table->unsignedBigInteger('location_id')->nullable(); // Ubah dari uuid menjadi unsignedBigInteger
                $table->unsignedBigInteger('owner_id')->nullable(); // Referensi ke user
                $table->enum('status', ['identified', 'assessed', 'treatment', 'monitored', 'closed'])->default('identified');
                $table->timestamp('date_identified')->nullable();
                $table->unsignedBigInteger('reporter_id')->nullable(); // Referensi ke user
                $table->uuid('current_assessment_id')->nullable(); // Referensi ke penilaian risiko terbaru
                $table->boolean('is_active')->default(true);
                $table->json('metadata')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('category_id')->references('id')->on('risk_categories')->onUpdate('cascade')->onDelete('restrict');
                $table->foreign('subcategory_id')->references('id')->on('risk_categories')->onUpdate('cascade')->onDelete('set null');
                $table->foreign('location_id')->references('id')->on('departments')->onUpdate('cascade')->onDelete('set null');
                $table->foreign('owner_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
                $table->foreign('reporter_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');

                $table->unique(['tenant_id', 'risk_code']);
            });
        }

        // Tabel Penilaian Risiko (time-series)
        if (!Schema::hasTable('risk_assessments')) {
            Schema::create('risk_assessments', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('risk_id');
                $table->integer('likelihood_score');
                $table->integer('impact_score');
                $table->enum('risk_level', ['low', 'medium', 'high', 'extreme']);
                $table->timestamp('assessment_date');
                $table->unsignedBigInteger('assessor_id')->nullable(); // Referensi ke user
                $table->text('rationale')->nullable();
                $table->boolean('inherent_assessment')->default(false);
                $table->boolean('residual_assessment')->default(false);
                $table->boolean('target_assessment')->default(false);
                $table->json('metadata')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->foreign('risk_id')->references('id')->on('risk_registry')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('assessor_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            });

            // Tambahkan foreign key untuk current_assessment_id
            Schema::table('risk_registry', function (Blueprint $table) {
                $table->foreign('current_assessment_id')->references('id')->on('risk_assessments')->onUpdate('cascade')->onDelete('set null');
            });
        }

        // Tabel Rencana Mitigasi
        if (!Schema::hasTable('mitigation_plans')) {
            Schema::create('mitigation_plans', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('risk_id');
                $table->string('title', 200);
                $table->text('description')->nullable();
                $table->enum('strategy', ['avoid', 'transfer', 'mitigate', 'accept'])->default('mitigate');
                $table->date('target_date')->nullable();
                $table->unsignedBigInteger('owner_id')->nullable(); // Referensi ke user
                $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
                $table->integer('effectiveness_rating')->nullable();
                $table->text('effectiveness_notes')->nullable();
                $table->json('metadata')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->foreign('risk_id')->references('id')->on('risk_registry')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('owner_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            });
        }

        // Tabel Tindakan Mitigasi
        if (!Schema::hasTable('mitigation_actions')) {
            Schema::create('mitigation_actions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('plan_id');
                $table->string('title', 200);
                $table->text('description')->nullable();
                $table->date('due_date')->nullable();
                $table->unsignedBigInteger('assignee_id')->nullable(); // Referensi ke user
                $table->enum('status', ['not_started', 'in_progress', 'completed', 'cancelled'])->default('not_started');
                $table->date('completion_date')->nullable();
                $table->text('completion_notes')->nullable();
                $table->json('metadata')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->foreign('plan_id')->references('id')->on('mitigation_plans')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('assignee_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            });
        }

        // Tabel Review Risiko
        if (!Schema::hasTable('risk_reviews')) {
            Schema::create('risk_reviews', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('risk_id');
                $table->timestamp('review_date');
                $table->unsignedBigInteger('reviewer_id')->nullable(); // Referensi ke user
                $table->text('comments')->nullable();
                $table->enum('status', ['pending', 'completed'])->default('pending');
                $table->date('next_review_date')->nullable();
                $table->json('metadata')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->foreign('risk_id')->references('id')->on('risk_registry')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('reviewer_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_reviews');
        Schema::dropIfExists('mitigation_actions');
        Schema::dropIfExists('mitigation_plans');

        if (Schema::hasTable('risk_registry') && Schema::hasColumn('risk_registry', 'current_assessment_id')) {
            Schema::table('risk_registry', function (Blueprint $table) {
                $table->dropForeign(['current_assessment_id']);
            });
        }

        Schema::dropIfExists('risk_assessments');
        Schema::dropIfExists('risk_registry');
        Schema::dropIfExists('risk_categories');
    }
};
