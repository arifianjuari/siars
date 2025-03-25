<?php

namespace App\Http\Controllers\DocumentManagement;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\MeetingMinute;
use App\Models\DocumentHistory;
use App\Models\DocumentRecipient;
use App\Models\Invitation;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MeetingMinuteController extends Controller
{
    /**
     * Display a listing of the meeting minutes.
     */
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $query = Document::with(['meetingMinutes', 'creator', 'signatures', 'attachments'])
            ->where('tenant_id', $tenantId)
            ->where('document_type', 'notulensi');

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
                    ->orWhereHas('meetingMinutes', function ($sq) use ($search) {
                        $sq->where('meeting_title', 'like', "%{$search}%")
                            ->orWhere('meeting_location', 'like', "%{$search}%");
                    });
            });
        }

        $minutes = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('document-management.meeting-minutes.index', compact('minutes'));
    }

    /**
     * Show the form for creating a new meeting minute.
     */
    public function create()
    {
        $tenantId = Auth::user()->tenant_id;
        $invitations = Document::with('invitation')
            ->where('tenant_id', $tenantId)
            ->where('document_type', 'undangan')
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('document-management.meeting-minutes.create', compact('invitations'));
    }

    /**
     * Store a newly created meeting minute in storage.
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
            'invitation_id' => 'nullable|exists:documents,id',
            'meeting_title' => 'required|string|max:255',
            'meeting_datetime' => 'required|date_format:Y-m-d H:i',
            'meeting_location' => 'required|string|max:255',
            'attendees' => 'required|string',
            'discussion_points' => 'required|string',
            'action_items' => 'nullable|string',
            'conclusions' => 'nullable|string',
            'recorder_name' => 'required|string|max:255',
            'attachments.*' => 'nullable|file|max:10240', // Max 10MB per file
        ]);

        DB::beginTransaction();

        try {
            // Generate document number
            $documentNumber = Document::generateDocumentNumber('notulensi', $tenantId);

            // Create document
            $document = Document::create([
                'tenant_id' => $tenantId,
                'document_number' => $documentNumber,
                'document_type' => 'notulensi',
                'subject' => $validated['subject'],
                'content' => $validated['content'],
                'reference_number' => $validated['reference_number'],
                'document_date' => $validated['document_date'],
                'status' => 'draft',
                'created_by' => $userId,
            ]);

            // Create meeting minute
            $meetingMinute = MeetingMinute::create([
                'document_id' => $document->id,
                'invitation_id' => $validated['invitation_id'],
                'meeting_title' => $validated['meeting_title'],
                'meeting_datetime' => $validated['meeting_datetime'],
                'meeting_location' => $validated['meeting_location'],
                'attendees' => $validated['attendees'],
                'discussion_points' => $validated['discussion_points'],
                'action_items' => $validated['action_items'],
                'conclusions' => $validated['conclusions'],
                'recorder_name' => $validated['recorder_name'],
            ]);

            // If related to an invitation, add invitation participants as recipients
            if ($validated['invitation_id']) {
                $invitation = Document::find($validated['invitation_id']);
                if ($invitation) {
                    $recipients = DocumentRecipient::where('document_id', $invitation->id)->get();
                    foreach ($recipients as $recipient) {
                        DocumentRecipient::create([
                            'document_id' => $document->id,
                            'department_id' => $recipient->department_id,
                            'user_id' => $recipient->user_id,
                            'recipient_type' => $recipient->recipient_type,
                        ]);
                    }
                }
            }

            // Record history
            DocumentHistory::create([
                'document_id' => $document->id,
                'action' => 'created',
                'description' => 'Notulensi rapat dibuat',
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

            return redirect()->route('document-management.meeting-minutes.show', $document->id)
                ->with('success', 'Notulensi rapat berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified meeting minute.
     */
    public function show(Document $meetingMinute)
    {
        $tenantId = Auth::user()->tenant_id;

        // Verifikasi bahwa dokumen milik tenant yang sama
        if ($meetingMinute->tenant_id != $tenantId) {
            return redirect()->route('document-management.meeting-minutes.index')
                ->with('error', 'Anda tidak memiliki akses ke notulensi ini.');
        }

        $meetingMinute->load(['meetingMinutes', 'creator', 'signatures', 'attachments', 'histories', 'recipients.department']);

        return view('document-management.meeting-minutes.show', compact('meetingMinute'));
    }

    /**
     * Show the form for editing the specified meeting minute.
     */
    public function edit(Document $meetingMinute)
    {
        $tenantId = Auth::user()->tenant_id;

        // Verifikasi bahwa dokumen milik tenant yang sama
        if ($meetingMinute->tenant_id != $tenantId) {
            return redirect()->route('document-management.meeting-minutes.index')
                ->with('error', 'Anda tidak memiliki akses ke notulensi ini.');
        }

        // Verifikasi bahwa dokumen masih draft
        if ($meetingMinute->status != 'draft') {
            return redirect()->route('document-management.meeting-minutes.show', $meetingMinute->id)
                ->with('error', 'Notulensi yang sudah dipublikasikan tidak dapat diedit.');
        }

        $meetingMinute->load(['meetingMinutes', 'attachments']);
        $invitations = Document::with('invitation')
            ->where('tenant_id', $tenantId)
            ->where('document_type', 'undangan')
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('document-management.meeting-minutes.edit', compact('meetingMinute', 'invitations'));
    }

    /**
     * Update the specified meeting minute in storage.
     */
    public function update(Request $request, Document $meetingMinute)
    {
        $tenantId = Auth::user()->tenant_id;
        $userId = Auth::id();

        // Verifikasi bahwa dokumen milik tenant yang sama
        if ($meetingMinute->tenant_id != $tenantId) {
            return redirect()->route('document-management.meeting-minutes.index')
                ->with('error', 'Anda tidak memiliki akses ke notulensi ini.');
        }

        // Verifikasi bahwa dokumen masih draft
        if ($meetingMinute->status != 'draft') {
            return redirect()->route('document-management.meeting-minutes.show', $meetingMinute->id)
                ->with('error', 'Notulensi yang sudah dipublikasikan tidak dapat diedit.');
        }

        // Validasi input
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'document_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'invitation_id' => 'nullable|exists:documents,id',
            'meeting_title' => 'required|string|max:255',
            'meeting_datetime' => 'required|date_format:Y-m-d H:i',
            'meeting_location' => 'required|string|max:255',
            'attendees' => 'required|string',
            'discussion_points' => 'required|string',
            'action_items' => 'nullable|string',
            'conclusions' => 'nullable|string',
            'recorder_name' => 'required|string|max:255',
            'attachments.*' => 'nullable|file|max:10240', // Max 10MB per file
        ]);

        DB::beginTransaction();

        try {
            // Update document
            $meetingMinute->update([
                'subject' => $validated['subject'],
                'content' => $validated['content'],
                'reference_number' => $validated['reference_number'],
                'document_date' => $validated['document_date'],
                'updated_by' => $userId,
            ]);

            // Update meeting minute
            $meetingMinute->meetingMinutes->update([
                'invitation_id' => $validated['invitation_id'],
                'meeting_title' => $validated['meeting_title'],
                'meeting_datetime' => $validated['meeting_datetime'],
                'meeting_location' => $validated['meeting_location'],
                'attendees' => $validated['attendees'],
                'discussion_points' => $validated['discussion_points'],
                'action_items' => $validated['action_items'],
                'conclusions' => $validated['conclusions'],
                'recorder_name' => $validated['recorder_name'],
            ]);

            // Record history
            DocumentHistory::create([
                'document_id' => $meetingMinute->id,
                'action' => 'updated',
                'description' => 'Notulensi rapat diperbarui',
                'performed_by' => $userId,
            ]);

            // Upload new attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = $file->getClientOriginalName();
                    $filePath = $file->store('document_attachments/' . $meetingMinute->id, 'public');
                    $fileType = $file->getClientOriginalExtension();
                    $fileSize = $file->getSize();

                    // Save attachment to database
                    $meetingMinute->attachments()->create([
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'file_type' => $fileType,
                        'file_size' => $fileSize,
                        'uploaded_by' => $userId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('document-management.meeting-minutes.show', $meetingMinute->id)
                ->with('success', 'Notulensi rapat berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Publish the specified meeting minute.
     */
    public function publish(Document $meetingMinute)
    {
        $tenantId = Auth::user()->tenant_id;
        $userId = Auth::id();

        // Verifikasi bahwa dokumen milik tenant yang sama
        if ($meetingMinute->tenant_id != $tenantId) {
            return redirect()->route('document-management.meeting-minutes.index')
                ->with('error', 'Anda tidak memiliki akses ke notulensi ini.');
        }

        // Verifikasi bahwa dokumen masih draft
        if ($meetingMinute->status != 'draft') {
            return redirect()->route('document-management.meeting-minutes.show', $meetingMinute->id)
                ->with('error', 'Notulensi ini sudah dipublikasikan sebelumnya.');
        }

        try {
            // Update status document
            $meetingMinute->update([
                'status' => 'published',
                'updated_by' => $userId,
            ]);

            // Record history
            DocumentHistory::create([
                'document_id' => $meetingMinute->id,
                'action' => 'published',
                'description' => 'Notulensi rapat dipublikasikan',
                'performed_by' => $userId,
            ]);

            // Generate QR Code jika diperlukan
            // Implementasi QR Code akan ditambahkan di sini

            return redirect()->route('document-management.meeting-minutes.show', $meetingMinute->id)
                ->with('success', 'Notulensi rapat berhasil dipublikasikan');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified meeting minute from storage.
     */
    public function destroy(Document $meetingMinute)
    {
        $tenantId = Auth::user()->tenant_id;

        // Verifikasi bahwa dokumen milik tenant yang sama
        if ($meetingMinute->tenant_id != $tenantId) {
            return redirect()->route('document-management.meeting-minutes.index')
                ->with('error', 'Anda tidak memiliki akses ke notulensi ini.');
        }

        // Verifikasi bahwa dokumen masih draft
        if ($meetingMinute->status != 'draft') {
            return redirect()->route('document-management.meeting-minutes.show', $meetingMinute->id)
                ->with('error', 'Notulensi yang sudah dipublikasikan tidak dapat dihapus.');
        }

        try {
            // Hapus file lampiran dari storage
            foreach ($meetingMinute->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            // Hapus dokumen (soft delete)
            $meetingMinute->delete();

            return redirect()->route('document-management.meeting-minutes.index')
                ->with('success', 'Notulensi rapat berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
