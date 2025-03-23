<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Upload Dokumen Pendukung</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
        
        .dropzone {
            border: 2px dashed #cbd5e1;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .dropzone.dragover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Page Heading -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Upload Dokumen Pendukung') }}
                    </h2>
                    <div>
                        @if(request()->has('element_id'))
                            <a href="{{ route('snars.assessment-elements.show', request('element_id')) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Kembali ke Elemen Penilaian') }}
                            </a>
                        @else
                            <a href="{{ route('snars.documents.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Kembali ke Daftar Dokumen') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            @if ($errors->any())
                                <div class="mb-4">
                                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                        <strong class="font-bold">{{ __('Terjadi kesalahan!') }}</strong>
                                        <ul class="mt-2 list-disc list-inside text-sm">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('snars.documents.store') }}" method="POST" enctype="multipart/form-data" x-data="documentUploadForm()">
                                @csrf
                                
                                @if(request()->has('element_id'))
                                    <input type="hidden" name="element_id" value="{{ request('element_id') }}">
                                @endif
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Document Type -->
                                    <div class="mb-6">
                                        <label for="document_type_id" class="block text-sm font-medium text-gray-700">{{ __('Jenis Dokumen') }} <span class="text-red-600">*</span></label>
                                        <select id="document_type_id" name="document_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                            <option value="">{{ __('Pilih Jenis Dokumen') }}</option>
                                            @foreach($documentTypes as $type)
                                                <option value="{{ $type->id }}" {{ old('document_type_id') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Document Number -->
                                    <div class="mb-6">
                                        <label for="document_number" class="block text-sm font-medium text-gray-700">{{ __('Nomor Dokumen') }} <span class="text-red-600">*</span></label>
                                        <input type="text" name="document_number" id="document_number" value="{{ old('document_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    </div>
                                </div>
                                
                                <!-- Title -->
                                <div class="mb-6">
                                    <label for="title" class="block text-sm font-medium text-gray-700">{{ __('Judul Dokumen') }} <span class="text-red-600">*</span></label>
                                    <input type="text" name="title" id="title" value="{{ old('title') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Version -->
                                    <div class="mb-6">
                                        <label for="version" class="block text-sm font-medium text-gray-700">{{ __('Versi') }} <span class="text-red-600">*</span></label>
                                        <input type="text" name="version" id="version" value="{{ old('version', '1.0') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    </div>
                                    
                                    <!-- Effective Date -->
                                    <div class="mb-6">
                                        <label for="effective_date" class="block text-sm font-medium text-gray-700">{{ __('Tanggal Berlaku') }} <span class="text-red-600">*</span></label>
                                        <input type="date" name="effective_date" id="effective_date" value="{{ old('effective_date', date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    </div>
                                </div>
                                
                                <!-- Description -->
                                <div class="mb-6">
                                    <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Deskripsi') }}</label>
                                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
                                </div>
                                
                                <!-- Assessment Element Assignment -->
                                @if(!request()->has('element_id'))
                                    <div class="mb-6" x-data="{ showElements: false }">
                                        <div class="flex items-center mb-2">
                                            <label class="block text-sm font-medium text-gray-700 mr-2">{{ __('Elemen Penilaian Terkait') }}</label>
                                            <button type="button" @click="showElements = !showElements" class="text-sm text-blue-600 hover:text-blue-800">
                                                <span x-show="!showElements">{{ __('Tampilkan') }}</span>
                                                <span x-show="showElements">{{ __('Sembunyikan') }}</span>
                                            </button>
                                        </div>
                                        
                                        <div x-show="showElements" class="border border-gray-200 rounded-md p-4 mt-2">
                                            <p class="text-sm text-gray-600 mb-3">{{ __('Pilih elemen penilaian yang terkait dengan dokumen ini:') }}</p>
                                            
                                            <div class="mb-4">
                                                <input type="text" x-model="searchTerm" placeholder="{{ __('Cari elemen penilaian...') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            </div>
                                            
                                            <div class="max-h-60 overflow-y-auto">
                                                <template x-for="(element, index) in filteredElements" :key="element.id">
                                                    <div class="flex items-center py-2 border-b border-gray-100">
                                                        <input :id="'element_' + element.id" name="elements[]" type="checkbox" :value="element.id" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                        <label :for="'element_' + element.id" class="ml-2 block text-sm text-gray-700">
                                                            <span class="font-medium" x-text="element.code"></span> - 
                                                            <span x-text="element.description"></span>
                                                            <span class="text-xs text-gray-500 block mt-1" x-text="element.standard"></span>
                                                        </label>
                                                    </div>
                                                </template>
                                                
                                                <div x-show="filteredElements.length === 0" class="py-2 text-center text-sm text-gray-500">
                                                    {{ __('Tidak ada elemen penilaian yang ditemukan.') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- File Upload -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Upload File') }} <span class="text-red-600">*</span></label>
                                    
                                    <div 
                                        class="dropzone" 
                                        x-bind:class="{ 'dragover': isDragging }"
                                        @dragover.prevent="isDragging = true"
                                        @dragleave.prevent="isDragging = false"
                                        @drop.prevent="dropHandler"
                                    >
                                        <div x-show="!fileSelected">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <p class="mt-2 text-sm text-gray-600">
                                                {{ __('Seret dan lepaskan file di sini, atau') }} 
                                                <button type="button" @click="document.getElementById('file-upload').click()" class="text-blue-600 hover:text-blue-800">
                                                    {{ __('pilih file') }}
                                                </button>
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ __('PDF, Word, Excel, atau PowerPoint hingga 10MB') }}
                                            </p>
                                        </div>
                                        
                                        <div x-show="fileSelected" class="text-left">
                                            <div class="flex items-center">
                                                <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                </svg>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900" x-text="fileName"></div>
                                                    <div class="text-xs text-gray-500" x-text="fileSize"></div>
                                                </div>
                                                <button type="button" @click="removeFile" class="ml-auto text-gray-400 hover:text-gray-500">
                                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <input id="file-upload" name="file" type="file" class="hidden" @change="fileInputHandler">
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Upload Dokumen') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        function documentUploadForm() {
            return {
                isDragging: false,
                fileSelected: false,
                fileName: '',
                fileSize: '',
                searchTerm: '',
                elements: @json($elements ?? []),
                
                get filteredElements() {
                    if (!this.searchTerm) return this.elements;
                    
                    const term = this.searchTerm.toLowerCase();
                    return this.elements.filter(element => 
                        element.code.toLowerCase().includes(term) || 
                        element.description.toLowerCase().includes(term) ||
                        element.standard.toLowerCase().includes(term)
                    );
                },
                
                fileInputHandler(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.setFileInfo(file);
                    }
                },
                
                dropHandler(event) {
                    this.isDragging = false;
                    const file = event.dataTransfer.files[0];
                    if (file) {
                        document.getElementById('file-upload').files = event.dataTransfer.files;
                        this.setFileInfo(file);
                    }
                },
                
                setFileInfo(file) {
                    this.fileSelected = true;
                    this.fileName = file.name;
                    
                    // Format file size
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    if (file.size === 0) {
                        this.fileSize = '0 Byte';
                    } else {
                        const i = parseInt(Math.floor(Math.log(file.size) / Math.log(1024)));
                        this.fileSize = Math.round(file.size / Math.pow(1024, i), 2) + ' ' + sizes[i];
                    }
                },
                
                removeFile() {
                    document.getElementById('file-upload').value = '';
                    this.fileSelected = false;
                    this.fileName = '';
                    this.fileSize = '';
                }
            };
        }
    </script>
</body>
</html>
