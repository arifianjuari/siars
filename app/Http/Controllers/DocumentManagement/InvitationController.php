<?php

namespace App\Http\Controllers\DocumentManagement;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Invitation;
use App\Models\DocumentHistory;
use App\Models\DocumentRecipient;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvitationController extends Controller
{
    /**
     * Display a listing of the invitations.
     */
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $query = Document::with(['invitation', 'creator', 'signatures', 'attachments'])
            ->where('tenant_id', $tenantId)
            ->where('document_type', 'undangan');

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('document_date', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('invitation', function ($sq) use ($search) {
                        $sq->where('meeting_title', 'like', "%{$search}%")
                            ->orWhere('meeting_location', 'like', "%{$search}%");
                    });
            });
        }

        $invitations = $query->orderBy('created_at', 'desc')->paginate(10);

        $departments = Department::where('tenant_id', $tenantId)->get();

        return view('document-management.invitations.index', compact('invitations', 'departments'));
    }

    /**
     * Show the form for creating a new invitation.
     */
    public function create()
    {
        $tenantId = Auth::user()->tenant_id;
        $departments = Department::where('tenant_id', $tenantId)->get();

        return view('document-management.invitations.create', compact('departments'));
    }

    /**
     * Store a newly created invitation in storage.
     */
    public function store(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $userId = Auth::id();

        // Validasi input
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'document_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'organizer_department_id' => 'required|exists:departments,id',
            'meeting_title' => 'required|string|max:255',
            'meeting_datetime' => 'required|date_format:Y-m-d H:i',
            'meeting_location' => 'required|string|max:255',
            'meeting_agenda' => 'nullable|string',
            'signatory_position' => 'nullable|string|max:255',
            'signatory_name' => 'nullable|string|max:255',
            'recipients' => 'required|array',
            'recipients.*.department_id' => 'required|exists:departments,id',
            'attachments.*' => 'nullable|file|max:10240', // Max 10MB per file
        ]);

        DB::beginTransaction();

        try {
            // Generate document number
            $documentNumber = Document::generateDocumentNumber('undangan', $tenantId);

            // Create document
            $document = Document::create([
                'tenant_id' => $tenantId,
                'document_number' => $documentNumber,
                'document_type' => 'undangan',
                'subject' => $validated['subject'],
                'content' => $validated['content'],
                'reference_number' => $validated['reference_number'],
                'document_date' => $validated['document_date'],
                'status' => 'draft',
                'created_by' => $userId,
            ]);

            // Create invitation
            $invitation = Invitation::create([
                'document_id' => $document->id,
                'organizer_department_id' => $validated['organizer_department_id'],
                'meeting_title' => $validated['meeting_title'],
                'meeting_datetime' => $validated['meeting_datetime'],
                'meeting_location' => $validated['meeting_location'],
                'meeting_agenda' => $validated['meeting_agenda'],
                'signatory_position' => $validated['signatory_position'],
                'signatory_name' => $validated['signatory_name'],
            ]);

            // Add recipients
            foreach ($validated['recipients'] as $recipient) {
                DocumentRecipient::create([
                    'document_id' => $document->id,
                    'department_id' => $recipient['department_id'],
                    'recipient_type' => 'department',
                ]);
            }

            // Record history
            DocumentHistory::create([
                'document_id' => $document->id,
                'action' => 'created',
                'description' => 'Undangan rapat dibuat',
                'performed_by' => $userId,
            ]);

            // Upload attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = $file->getClientOriginalName();
                    $filePath = $file->store('document_attachments/' . $document->id, 'public');
                    $fileType = $file->getClientOriginalExtension();
                    $fileSize = $file->getSize();

                    // Save attachment to database
                    $document->attachments()->create([
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'file_type' => $fileType,
                        'file_size' => $fileSize,
                        'uploaded_by' => $userId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('document-management.invitations.show', $document->id)
                ->with('success', 'Undangan rapat berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified invitation.
     */
    public function show(Document $document)
    {
        $tenantId = Auth::user()->tenant_id;

        // Verifikasi bahwa dokumen milik tenant yang sama
        if ($document->tenant_id != $tenantId) {
            return redirect()->route('document-management.invitations.index')
                ->with('error', 'Anda tidak memiliki akses ke undangan ini.');
        }

        $document->load(['invitation', 'creator', 'signatures', 'attachments', 'histories', 'recipients.department']);

        // Perbarui status baca jika ada
        $recipient = DocumentRecipient::where('document_id', $document->id)
            ->where(function ($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereHas('department', function ($q) {
                        $q->whereIn('id', Auth::user()->departments->pluck('id'));
                    });
            })
            ->first();

        if ($recipient && !$recipient->is_read) {
            $recipient->is_read = true;
            $recipient->read_at = now();
            $recipient->save();
        }

        return view('document-management.invitations.show', compact('document'));
    }

    /**
     * Show the form for editing the specified invitation.
     */
    public function edit(Document $document)
    {
        $tenantId = Auth::user()->tenant_id;

        // Verifikasi bahwa dokumen milik tenant yang sama
        if ($document->tenant_id != $tenantId) {
            return redirect()->route('document-management.invitations.index')
                ->with('error', 'Anda tidak memiliki akses ke undangan ini.');
        }

        // Verifikasi bahwa dokumen masih draft
        if ($document->status != 'draft') {
            return redirect()->route('document-management.invitations.show', $document->id)
                ->with('error', 'Undangan yang sudah dipublikasikan tidak dapat diedit.');
        }

        $document->load(['invitation', 'attachments', 'recipients.department']);
        $departments = Department::where('tenant_id', $tenantId)->get();

        return view('document-management.invitations.edit', compact('document', 'departments'));
    }

    /**
     * Update the specified invitation in storage.
     */
    public function update(Request $request, Document $document)
    {
        $tenantId = Auth::user()->tenant_id;
        $userId = Auth::id();

        // Verifikasi bahwa dokumen milik tenant yang sama
        if ($document->tenant_id != $tenantId) {
            return redirect()->route('document-management.invitations.index')
                ->with('error', 'Anda tidak memiliki akses ke undangan ini.');
        }

        // Verifikasi bahwa dokumen masih draft
        if ($document->status != 'draft') {
            return redirect()->route('document-management.invitations.show', $document->id)
                ->with('error', 'Undangan yang sudah dipublikasikan tidak dapat diedit.');
        }

        // Validasi input
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'document_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'organizer_department_id' => 'required|exists:departments,id',
            'meeting_title' => 'required|string|max:255',
            'meeting_datetime' => 'required|date_format:Y-m-d H:i',
            'meeting_location' => 'required|string|max:255',
            'meeting_agenda' => 'nullable|string',
            'signatory_position' => 'nullable|string|max:255',
            'signatory_name' => 'nullable|string|max:255',
            'recipients' => 'required|array',
            'recipients.*.department_id' => 'required|exists:departments,id',
            'attachments.*' => 'nullable|file|max:10240', // Max 10MB per file
        ]);

        DB::beginTransaction();

        try {
            // Update document
            $document->update([
                'subject' => $validated['subject'],
                'content' => $validated['content'],
                'reference_number' => $validated['reference_number'],
                'document_date' => $validated['document_date'],
                'updated_by' => $userId,
            ]);

            // Update invitation
            $document->invitation->update([
                'organizer_department_id' => $validated['organizer_department_id'],
                'meeting_title' => $validated['meeting_title'],
                'meeting_datetime' => $validated['meeting_datetime'],
                'meeting_location' => $validated['meeting_location'],
                'meeting_agenda' => $validated['meeting_agenda'],
                'signatory_position' => $validated['signatory_position'],
                'signatory_name' => $validated['signatory_name'],
            ]);

            // Hapus semua penerima lama
            $document->recipients()->delete();

            // Tambahkan penerima baru
            foreach ($validated['recipients'] as $recipient) {
                DocumentRecipient::create([
                    'document_id' => $document->id,
                    'department_id' => $recipient['department_id'],
                    'recipient_type' => 'department',
                ]);
            }

            // Record history
            DocumentHistory::create([
                'document_id' => $document->id,
                'action' => 'updated',
                'description' => 'Undangan rapat diperbarui',
                'performed_by' => $userId,
            ]);

            // Upload new attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = $file->getClientOriginalName();
                    $filePath = $file->store('document_attachments/' . $document->id, 'public');
                    $fileType = $file->getClientOriginalExtension();
                    $fileSize = $file->getSize();

                    // Save attachment to database
                    $document->attachments()->create([
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'file_type' => $fileType,
                        'file_size' => $fileSize,
                        'uploaded_by' => $userId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('document-management.invitations.show', $document->id)
                ->with('success', 'Undangan rapat berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Publish the specified invitation.
     */
    public function publish(Document $document)
    {
        $tenantId = Auth::user()->tenant_id;
        $userId = Auth::id();

        // Verifikasi bahwa dokumen milik tenant yang sama
        if ($document->tenant_id != $tenantId) {
            return redirect()->route('document-management.invitations.index')
                ->with('error', 'Anda tidak memiliki akses ke undangan ini.');
        }

        // Verifikasi bahwa dokumen masih draft
        if ($document->status != 'draft') {
            return redirect()->route('document-management.invitations.show', $document->id)
                ->with('error', 'Undangan ini sudah dipublikasikan sebelumnya.');
        }

        try {
            // Update status document
            $document->update([
                'status' => 'published',
                'updated_by' => $userId,
            ]);

            // Record history
            DocumentHistory::create([
                'document_id' => $document->id,
                'action' => 'published',
                'description' => 'Undangan rapat dipublikasikan',
                'performed_by' => $userId,
            ]);

            // Generate QR Code jika diperlukan
            // Implementasi QR Code akan ditambahkan di sini

            return redirect()->route('document-management.invitations.show', $document->id)
                ->with('success', 'Undangan rapat berhasil dipublikasikan');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified invitation from storage.
     */
    public function destroy(Document $document)
    {
        $tenantId = Auth::user()->tenant_id;

        // Verifikasi bahwa dokumen milik tenant yang sama
        if ($document->tenant_id != $tenantId) {
            return redirect()->route('document-management.invitations.index')
                ->with('error', 'Anda tidak memiliki akses ke undangan ini.');
        }

        // Verifikasi bahwa dokumen masih draft
        if ($document->status != 'draft') {
            return redirect()->route('document-management.invitations.show', $document->id)
                ->with('error', 'Undangan yang sudah dipublikasikan tidak dapat dihapus.');
        }

        try {
            // Hapus file lampiran dari storage
            foreach ($document->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            // Hapus dokumen (soft delete)
            $document->delete();

            return redirect()->route('document-management.invitations.index')
                ->with('success', 'Undangan rapat berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
