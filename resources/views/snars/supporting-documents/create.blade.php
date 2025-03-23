<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah Dokumen Pendukung') }}
            </h2>
            <div>
                <a href="{{ route('snars.supporting-documents.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Kembali') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('snars.supporting-documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <!-- Document Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dokumen</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Title -->
                                <div>
                                    <x-label for="title" :value="__('Judul Dokumen')" />
                                    <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                                </div>

                                <!-- Assessment Element -->
                                <div>
                                    <x-label for="assessment_element_id" :value="__('Elemen Penilaian')" />
                                    <select id="assessment_element_id" name="assessment_element_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        <option value="">Pilih Elemen Penilaian</option>
                                        @foreach ($assessmentElements as $element)
                                            <option value="{{ $element->id }}" {{ old('assessment_element_id') == $element->id ? 'selected' : '' }}>
                                                {{ $element->code }} - {{ Str::limit($element->description, 100) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Description -->
                                <div class="md:col-span-2">
                                    <x-label for="description" :value="__('Deskripsi')" />
                                    <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Unggah File</h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <x-label for="document_file" :value="__('File Dokumen')" />
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="document_file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Unggah file</span>
                                                    <input id="document_file" name="document_file" type="file" class="sr-only" required>
                                                </label>
                                                <p class="pl-1">atau seret dan lepas</p>
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                PDF, Word, Excel, PowerPoint, atau gambar hingga 10MB
                                            </p>
                                        </div>
                                    </div>
                                    <div id="file-preview" class="mt-2 hidden">
                                        <div class="flex items-center space-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span id="file-name" class="text-sm text-gray-500"></span>
                                            <span id="file-size" class="text-xs text-gray-400"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <x-button>
                                {{ __('Simpan Dokumen') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('document_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const preview = document.getElementById('file-preview');
                const fileName = document.getElementById('file-name');
                const fileSize = document.getElementById('file-size');
                
                preview.classList.remove('hidden');
                fileName.textContent = file.name;
                
                // Format file size
                const size = file.size;
                let formattedSize;
                if (size < 1024) {
                    formattedSize = size + ' bytes';
                } else if (size < 1024 * 1024) {
                    formattedSize = (size / 1024).toFixed(2) + ' KB';
                } else {
                    formattedSize = (size / (1024 * 1024)).toFixed(2) + ' MB';
                }
                fileSize.textContent = '(' + formattedSize + ')';
            }
        });
    </script>
</x-app-layout>
