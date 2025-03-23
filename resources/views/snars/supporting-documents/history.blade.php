<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Versi Dokumen') }}
            </h2>
            <div>
                <a href="{{ route('snars.supporting-documents.show', $document->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Kembali ke Detail Dokumen') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Document Information -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Informasi Dokumen</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Judul</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $document->title }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Elemen Penilaian</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $document->assessmentElement->code ?? 'N/A' }} - {{ Str::limit($document->assessmentElement->description ?? '', 50) }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Versi Saat Ini</h4>
                                <p class="mt-1 text-sm text-gray-900">v{{ $document->version }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Version History Timeline -->
                    <div class="relative">
                        <div class="absolute left-5 top-0 h-full w-0.5 bg-gray-200"></div>
                        
                        <div class="space-y-8">
                            @forelse ($versions as $version)
                                <div class="relative">
                                    <div class="flex items-start">
                                        <!-- Timeline Dot -->
                                        <div class="absolute left-0 mt-1.5">
                                            <div class="h-10 w-10 flex items-center justify-center rounded-full {{ $version->id === $document->id ? 'bg-blue-500' : 'bg-gray-300' }}">
                                                <span class="text-xs font-bold text-white">v{{ $version->version }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Version Content -->
                                        <div class="ml-16 bg-white p-4 rounded-lg border {{ $version->id === $document->id ? 'border-blue-300 ring-1 ring-blue-300' : 'border-gray-200' }}">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h3 class="text-lg font-medium text-gray-900">
                                                        {{ $version->title }}
                                                        @if ($version->id === $document->id)
                                                            <span class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Versi Saat Ini</span>
                                                        @endif
                                                    </h3>
                                                    <p class="text-sm text-gray-500">
                                                        Dibuat oleh {{ $version->creator->name ?? 'N/A' }} pada {{ $version->created_at->format('d M Y H:i') }}
                                                    </p>
                                                </div>
                                                <div>
                                                    @if ($version->status === 'draft')
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                                                    @elseif ($version->status === 'review')
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Dalam Review</span>
                                                    @elseif ($version->status === 'approved')
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Disetujui</span>
                                                    @elseif ($version->status === 'rejected')
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Ditolak</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-500">Deskripsi</h4>
                                                    <p class="mt-1 text-sm text-gray-900">{{ $version->description ?? 'Tidak ada deskripsi' }}</p>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-500">File</h4>
                                                    <div class="mt-1 flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center bg-gray-100 rounded-md">
                                                            @if ($version->isPdf())
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                                </svg>
                                                            @elseif ($version->isImage())
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                            @else
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                </svg>
                                                            @endif
                                                        </div>
                                                        <div class="ml-2">
                                                            <p class="text-sm text-gray-900">{{ $version->file_name }}</p>
                                                            <p class="text-xs text-gray-500">{{ $version->file_type }} · {{ number_format($version->file_size / 1024, 2) }} KB</p>
                                                        </div>
                                                        <div class="ml-auto">
                                                            <a href="{{ route('snars.supporting-documents.download', $version->id) }}" class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                                </svg>
                                                                Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if ($version->status === 'approved' && $version->approver)
                                                <div class="mt-4 p-3 bg-green-50 rounded-md">
                                                    <div class="flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span class="ml-2 text-sm font-medium text-green-800">Disetujui oleh {{ $version->approver->name }} pada {{ $version->approved_at->format('d M Y H:i') }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if ($version->status === 'rejected' && $version->rejection_reason)
                                                <div class="mt-4 p-3 bg-red-50 rounded-md">
                                                    <div class="flex">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                        <div class="ml-2">
                                                            <span class="text-sm font-medium text-red-800">Ditolak</span>
                                                            <p class="text-sm text-red-700">{{ $version->rejection_reason }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <div class="mt-4 flex justify-end space-x-2">
                                                <a href="{{ route('snars.supporting-documents.show', $version->id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Lihat Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6">
                                    <p class="text-gray-500">Tidak ada riwayat versi untuk dokumen ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
