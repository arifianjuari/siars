<?php

namespace App\Http\Controllers\DocumentManagement;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Memo;
use App\Models\DocumentHistory;
use App\Models\DocumentRecipient;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MemoController extends Controller
{
    /**
     * Display a listing of the memos.
     */
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $type = $request->type ?? 'nota_dinas_masuk';

        $query = Document::with(['memo', 'creator', 'signatures', 'attachments'])
            ->where('tenant_id', $tenantId)
            ->where('document_type', $type);

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
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        $memos = $query->orderBy('created_at', 'desc')->paginate(10);

        $departments = Department::where('tenant_id', $tenantId)->get();

        return view('document-management.memos.index', compact('memos', 'departments', 'type'));
    }

    /**
     * Show the form for creating a new memo.
     */
    public function create(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $type = $request->type ?? 'nota_dinas_masuk';
        $departments = Department::where('tenant_id', $tenantId)->get();

        return view('document-management.memos.create', compact('departments', 'type'));
    }

    /**
     * Store a newly created memo in storage.
     */
    public function store(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $userId = Auth::id();

        // Validasi input
        $validated = $request->validate([
            'type' => 'required|in:nota_dinas_masuk,nota_dinas_keluar',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'document_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'sender_department_id' => 'required|exists:departments,id',
            'recipient_department_id' => 'required|exists:departments,id',
            'sender_name' => 'nullable|string|max:255',
            'recipient_name' => 'nullable|string|max:255',
            'reference_regulation' => 'nullable|string',
            'place' => 'nullable|string|max:255',
            'signatory_position' => 'nullable|string|max:255',
            'signatory_name' => 'nullable|string|max:255',
            'rank' => 'nullable|string|max:100',
            'nrp' => 'nullable|string|max:100',
            'carbon_copy' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240', // Max 10MB per file
        ]);

        DB::beginTransaction();

        try {
            // Generate document number
            $documentNumber = Document::generateDocumentNumber($validated['type'], $tenantId);

            // Create document
            $document = Document::create([
                'tenant_id' => $tenantId,
                'document_number' => $documentNumber,
                'document_type' => $validated['type'],
                'subject' => $validated['subject'],
                'content' => $validated['content'],
                'reference_number' => $validated['reference_number'],
                'document_date' => $validated['document_date'],
                'status' => 'draft',
                'created_by' => $userId,
            ]);

            // Create memo
            $memo = Memo::create([
                'document_id' => $document->id,
                'sender_department_id' => $validated['sender_department_id'],
                'recipient_department_id' => $validated['recipient_department_id'],
                'sender_name' => $validated['sender_name'],
                'recipient_name' => $validated['recipient_name'],
                'reference_regulation' => $validated['reference_regulation'],
                'place' => $validated['place'],
                'signatory_position' => $validated['signatory_position'],
                'signatory_name' => $validated['signatory_name'],
                'rank' => $validated['rank'],
                'nrp' => $validated['nrp'],
                'carbon_copy' => $validated['carbon_copy'],
            ]);

            // Add recipient
            DocumentRecipient::create([
                'document_id' => $document->id,
                'department_id' => $validated['recipient_department_id'],
                'recipient_type' => 'department',
            ]);

            // Record history
            DocumentHistory::create([
                'document_id' => $document->id,
                'action' => 'created',
                'description' => 'Dokumen nota dinas dibuat',
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

            return redirect()->route('document-management.memos.show', $document->id)
                ->with('success', 'Nota Dinas berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified memo.
     */
    public function show(Document $document)
    {
        $tenantId = Auth::user()->tenant_id;

        // Verifikasi bahwa dokumen milik tenant yang sama
        if ($document->tenant_id != $tenantId) {
            return redirect()->route('document-management.memos.index')
                ->with('error', 'Anda tidak memiliki akses ke dokumen ini.');
        }

        $document->load(['memo', 'creator', 'signatures', 'attachments', 'histories']);

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

        return view('document-management.memos.show', compact('document'));
    }

    /**
     * Show the form for editing the specified memo.
     */
    public function edit(Document $document)
    {
        $tenantId = Auth::user()->tenant_id;

        // Verifikasi bahwa dokumen milik tenant yang sama
        if ($document->tenant_id != $tenantId) {
            return redirect()->route('document-management.memos.index')
                ->with('error', 'Anda tidak memiliki akses ke dokumen ini.');
        }

        // Verifikasi bahwa dokumen masih draft
        if ($document->status != 'draft') {
            return redirect()->route('document-management.memos.show', $document->id)
                ->with('error', 'Dokumen yang sudah dipublikasikan tidak dapat diedit.');
        }

        $document->load(['memo', 'attachments']);
        $departments = Department::where('tenant_id', $tenantId)->get();

        return view('document-management.memos.edit', compact('document', 'departments'));
    }

    /**
     * Update the specified memo in storage.
     */
    public function update(Request $request, Document $document)
    {
        $tenantId = Auth::user()->tenant_id;
        $userId = Auth::id();

        // Verifikasi bahwa dokumen milik tenant yang sama
        if ($document->tenant_id != $tenantId) {
            return redirect()->route('document-management.memos.index')
                ->with('error', 'Anda tidak memiliki akses ke dokumen ini.');
        }

        // Verifikasi bahwa dokumen masih draft
        if ($document->status != 'draft') {
            return redirect()->route('document-management.memos.show', $document->id)
                ->with('error', 'Dokumen yang sudah dipublikasikan tidak dapat diedit.');
        }

        // Validasi input
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'document_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'sender_department_id' => 'required|exists:departments,id',
            'recipient_department_id' => 'required|exists:departments,id',
            'sender_name' => 'nullable|string|max:255',
            'recipient_name' => 'nullable|string|max:255',
            'reference_regulation' => 'nullable|string',
            'place' => 'nullable|string|max:255',
            'signatory_position' => 'nullable|string|max:255',
            'signatory_name' => 'nullable|string|max:255',
            'rank' => 'nullable|string|max:100',
            'nrp' => 'nullable|string|max:100',
            'carbon_copy' => 'nullable|string',
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

            // Update memo
            $document->memo->update([
                'sender_department_id' => $validated['sender_department_id'],
                'recipient_department_id' => $validated['recipient_department_id'],
                'sender_name' => $validated['sender_name'],
                'recipient_name' => $validated['recipient_name'],
                'reference_regulation' => $validated['reference_regulation'],
                'place' => $validated['place'],
                'signatory_position' => $validated['signatory_position'],
                'signatory_name' => $validated['signatory_name'],
                'rank' => $validated['rank'],
                'nrp' => $validated['nrp'],
                'carbon_copy' => $validated['carbon_copy'],
            ]);

            // Update recipient
            $recipient = DocumentRecipient::where('document_id', $document->id)
                ->where('recipient_type', 'department')
                ->first();

            if ($recipient) {
                $recipient->update([
                    'department_id' => $validated['recipient_department_id'],
                ]);
            } else {
                DocumentRecipient::create([
                    'document_id' => $document->id,
                    'department_id' => $validated['recipient_department_id'],
                    'recipient_type' => 'department',
                ]);
            }

            // Record history
            DocumentHistory::create([
                'document_id' => $document->id,
                'action' => 'updated',
                'description' => 'Dokumen nota dinas diperbarui',
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

            return redirect()->route('document-management.memos.show', $document->id)
                ->with('success', 'Nota Dinas berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Publish the specified memo.
     */
    public function publish(Document $document)
    {
        $tenantId = Auth::user()->tenant_id;
        $userId = Auth::id();

        // Verifikasi bahwa dokumen milik tenant yang sama
        if ($document->tenant_id != $tenantId) {
            return redirect()->route('document-management.memos.index')
                ->with('error', 'Anda tidak memiliki akses ke dokumen ini.');
        }

        // Verifikasi bahwa dokumen masih draft
        if ($document->status != 'draft') {
            return redirect()->route('document-management.memos.show', $document->id)
                ->with('error', 'Dokumen ini sudah dipublikasikan sebelumnya.');
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
                'description' => 'Dokumen nota dinas dipublikasikan',
                'performed_by' => $userId,
            ]);

            // Generate QR Code jika diperlukan
            // Implementasi QR Code akan ditambahkan di sini

            return redirect()->route('document-management.memos.show', $document->id)
                ->with('success', 'Nota Dinas berhasil dipublikasikan');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified memo from storage.
     */
    public function destroy(Document $document)
    {
        $tenantId = Auth::user()->tenant_id;

        // Verifikasi bahwa dokumen milik tenant yang sama
        if ($document->tenant_id != $tenantId) {
            return redirect()->route('document-management.memos.index')
                ->with('error', 'Anda tidak memiliki akses ke dokumen ini.');
        }

        // Verifikasi bahwa dokumen masih draft
        if ($document->status != 'draft') {
            return redirect()->route('document-management.memos.show', $document->id)
                ->with('error', 'Dokumen yang sudah dipublikasikan tidak dapat dihapus.');
        }

        try {
            // Hapus file lampiran dari storage
            foreach ($document->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            // Hapus dokumen (soft delete)
            $document->delete();

            return redirect()->route('document-management.memos.index')
                ->with('success', 'Nota Dinas berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
