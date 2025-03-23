<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Dokumen Pendukung') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('snars.supporting-documents.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Kembali') }}
                </a>
                <a href="{{ route('snars.supporting-documents.download', $document->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    {{ __('Download') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Status and Action Buttons -->
                    <div class="mb-6 flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <div>
                                <span class="text-sm text-gray-500">Status:</span>
                                @if ($document->status === 'draft')
                                    <span class="ml-2 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Draft
                                    </span>
                                @elseif ($document->status === 'review')
                                    <span class="ml-2 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Dalam Review
                                    </span>
                                @elseif ($document->status === 'approved')
                                    <span class="ml-2 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                @elseif ($document->status === 'rejected')
                                    <span class="ml-2 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Ditolak
                                    </span>
                                @endif
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Versi:</span>
                                <span class="ml-2 text-sm font-medium">v{{ $document->version }}</span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            @if ($document->status === 'draft')
                                <a href="{{ route('snars.supporting-documents.edit', $document->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    {{ __('Edit') }}
                                </a>
                                <form action="{{ route('snars.supporting-documents.submit-for-review', $document->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('Ajukan Review') }}
                                    </button>
                                </form>
                            @elseif ($document->status === 'review')
                                @can('approve documents')
                                <form action="{{ route('snars.supporting-documents.approve', $document->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ __('Setujui') }}
                                    </button>
                                </form>
                                <button type="button" onclick="toggleRejectModal()" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    {{ __('Tolak') }}
                                </button>
                                @endcan
                            @endif
                            <a href="{{ route('snars.supporting-documents.create-version', $document->id) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                </svg>
                                {{ __('Buat Versi Baru') }}
                            </a>
                            <a href="{{ route('snars.supporting-documents.history', $document->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Riwayat Versi') }}
                            </a>
                        </div>
                    </div>

                    <!-- Document Details -->
                    <div class="bg-gray-50 p-6 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dokumen</h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Judul</h4>
                                        <p class="mt-1 text-sm text-gray-900">{{ $document->title }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Deskripsi</h4>
                                        <p class="mt-1 text-sm text-gray-900">{{ $document->description ?? 'Tidak ada deskripsi' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Nama File</h4>
                                        <p class="mt-1 text-sm text-gray-900">{{ $document->file_name }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Tipe File</h4>
                                        <p class="mt-1 text-sm text-gray-900">{{ $document->file_type }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Ukuran File</h4>
                                        <p class="mt-1 text-sm text-gray-900">{{ number_format($document->file_size / 1024, 2) }} KB</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Elemen Penilaian</h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Kode Elemen</h4>
                                        <p class="mt-1 text-sm text-gray-900">{{ $document->assessmentElement->code ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Deskripsi Elemen</h4>
                                        <p class="mt-1 text-sm text-gray-900">{{ $document->assessmentElement->description ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Standar</h4>
                                        <p class="mt-1 text-sm text-gray-900">{{ $document->assessmentElement->standard->code ?? 'N/A' }} - {{ $document->assessmentElement->standard->name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Bab</h4>
                                        <p class="mt-1 text-sm text-gray-900">{{ $document->assessmentElement->standard->chapter->code ?? 'N/A' }} - {{ $document->assessmentElement->standard->chapter->name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Kelompok</h4>
                                        <p class="mt-1 text-sm text-gray-900">{{ $document->assessmentElement->standard->chapter->group->code ?? 'N/A' }} - {{ $document->assessmentElement->standard->chapter->group->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Document Preview -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Preview Dokumen</h3>
                        <div class="border border-gray-300 rounded-lg overflow-hidden">
                            @if ($document->isPdf())
                                <div class="aspect-w-16 aspect-h-9">
                                    <iframe src="{{ asset('storage/' . $document->file_path) }}" class="w-full h-full"></iframe>
                                </div>
                            @elseif ($document->isImage())
                                <div class="flex justify-center">
                                    <img src="{{ asset('storage/' . $document->file_path) }}" alt="{{ $document->title }}" class="max-w-full h-auto">
                                </div>
                            @else
                                <div class="p-6 flex justify-center items-center">
                                    <div class="text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Preview tidak tersedia untuk tipe file ini.</p>
                                        <a href="{{ route('snars.supporting-documents.download', $document->id) }}" class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            {{ __('Download File') }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Rejection Information (if rejected) -->
                    @if ($document->status === 'rejected' && $document->rejection_reason)
                        <div class="mt-6 bg-red-50 p-6 rounded-lg border border-red-200">
                            <h3 class="text-lg font-medium text-red-800 mb-2">Alasan Penolakan</h3>
                            <p class="text-sm text-red-700">{{ $document->rejection_reason }}</p>
                        </div>
                    @endif

                    <!-- Approval Information (if approved) -->
                    @if ($document->status === 'approved' && $document->approver)
                        <div class="mt-6 bg-green-50 p-6 rounded-lg border border-green-200">
                            <h3 class="text-lg font-medium text-green-800 mb-2">Informasi Persetujuan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-sm font-medium text-green-700">Disetujui Oleh</h4>
                                    <p class="mt-1 text-sm text-green-800">{{ $document->approver->name }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-green-700">Tanggal Persetujuan</h4>
                                    <p class="mt-1 text-sm text-green-800">{{ $document->approved_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Tolak Dokumen</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Silakan berikan alasan penolakan dokumen ini. Alasan ini akan ditampilkan kepada pengunggah dokumen.</p>
                            <form id="rejectForm" action="{{ route('snars.supporting-documents.reject', $document->id) }}" method="POST" class="mt-4">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Alasan Penolakan</label>
                                    <textarea id="rejection_reason" name="rejection_reason" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="submitRejectForm()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Tolak
                </button>
                <button type="button" onclick="toggleRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleRejectModal() {
            const modal = document.getElementById('rejectModal');
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        function submitRejectForm() {
            const form = document.getElementById('rejectForm');
            form.submit();
        }
    </script>
</x-app-layout>
