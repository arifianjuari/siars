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
        // Tabel untuk dokumen
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('document_number')->unique();
            $table->string('document_type'); // 'nota_dinas_masuk', 'nota_dinas_keluar', 'undangan', 'notulensi'
            $table->string('subject');
            $table->text('content')->nullable();
            $table->string('reference_number')->nullable();
            $table->date('document_date');
            $table->string('status')->default('draft'); // draft, published, archived
            $table->string('qr_code_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel untuk surat (nota dinas masuk dan keluar)
        Schema::create('memos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('sender_department_id')->nullable()->constrained('departments');
            $table->foreignId('recipient_department_id')->nullable()->constrained('departments');
            $table->string('sender_name')->nullable();
            $table->string('recipient_name')->nullable();
            $table->text('reference_regulation')->nullable();
            $table->string('place')->nullable();
            $table->string('signatory_position')->nullable();
            $table->string('signatory_name')->nullable();
            $table->string('rank')->nullable();
            $table->string('nrp')->nullable();
            $table->text('carbon_copy')->nullable();
            $table->timestamps();
        });

        // Tabel untuk undangan
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('organizer_department_id')->nullable()->constrained('departments');
            $table->string('meeting_title');
            $table->datetime('meeting_datetime');
            $table->string('meeting_location');
            $table->text('meeting_agenda')->nullable();
            $table->string('signatory_position')->nullable();
            $table->string('signatory_name')->nullable();
            $table->timestamps();
        });

        // Tabel untuk notulensi rapat
        Schema::create('meeting_minutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('invitation_id')->nullable()->constrained('invitations');
            $table->string('meeting_title');
            $table->datetime('meeting_datetime');
            $table->string('meeting_location');
            $table->text('attendees')->nullable();
            $table->text('discussion_points')->nullable();
            $table->text('action_items')->nullable();
            $table->text('conclusions')->nullable();
            $table->string('recorder_name')->nullable();
            $table->timestamps();
        });

        // Tabel untuk riwayat dokumen
        Schema::create('document_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->string('action'); // created, updated, signed, shared, viewed
            $table->text('description')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Tabel untuk lampiran dokumen
        Schema::create('document_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Tabel untuk penerima dokumen
        Schema::create('document_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('recipient_type'); // department, user
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Tabel untuk tanda tangan dokumen
        Schema::create('document_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->string('signature_path')->nullable();
            $table->string('qr_code_path');
            $table->string('signature_hash');
            $table->timestamp('signed_at');
            $table->string('status'); // pending, signed, rejected
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Tabel untuk notifikasi dokumen
        Schema::create('document_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->string('notification_type'); // new_document, signature_request, document_signed, etc
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_notifications');
        Schema::dropIfExists('document_signatures');
        Schema::dropIfExists('document_recipients');
        Schema::dropIfExists('document_attachments');
        Schema::dropIfExists('document_histories');
        Schema::dropIfExists('meeting_minutes');
        Schema::dropIfExists('invitations');
        Schema::dropIfExists('memos');
        Schema::dropIfExists('documents');
    }
};
