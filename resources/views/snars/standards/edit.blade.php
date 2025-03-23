<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Edit Standar SNARS</title>

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
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Page Heading -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <div class="mb-2 text-sm text-gray-500">
                    <a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a>
                    <span class="mx-1">/</span>
                    <span class="text-gray-700">SNARS</span>
                    <span class="mx-1">/</span>
                    <a href="{{ route('snars.groups.index') }}" class="hover:underline">Kelompok</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('snars.groups.show', $standard->chapter->group) }}" class="hover:underline">{{ $standard->chapter->group->code }}</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('snars.chapters.show', $standard->chapter) }}" class="hover:underline">{{ $standard->chapter->code }}</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('snars.standards.show', $standard) }}" class="hover:underline">{{ $standard->code }}</a>
                    <span class="mx-1">/</span>
                    <span class="text-gray-700">Edit</span>
                </div>
            
                <div class="flex justify-between items-center">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Edit Standar SNARS') }}
                    </h2>
                    <div>
                        <a href="{{ route('snars.standards.show', $standard) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Kembali ke Detail') }}
                        </a>
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
                            <!-- Form Errors -->
                            @if ($errors->any())
                                <div class="mb-4 bg-red-50 p-4 rounded-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">
                                                {{ __('Terdapat kesalahan pada input yang diberikan:') }}
                                            </h3>
                                            <div class="mt-2 text-sm text-red-700">
                                                <ul class="list-disc pl-5 space-y-1">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Edit Form -->
                            <form method="POST" action="{{ route('snars.standards.update', $standard) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="group_id" value="{{ $standard->chapter->group_id }}" id="group_id">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Bab SNARS -->
                                    <div>
                                        <label for="chapter_id" class="block text-sm font-medium text-gray-700">{{ __('Bab SNARS') }} <span class="text-red-500">*</span></label>
                                        <select id="chapter_id" name="chapter_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                            <option value="">{{ __('Pilih Bab') }}</option>
                                            @foreach ($chapters as $chapter)
                                                <option value="{{ $chapter->id }}" {{ old('chapter_id', $standard->chapter_id) == $chapter->id ? 'selected' : '' }}>
                                                    {{ $chapter->code }} - {{ $chapter->title }} ({{ $chapter->group->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Kode -->
                                    <div>
                                        <label for="code" class="block text-sm font-medium text-gray-700">{{ __('Kode') }} <span class="text-red-500">*</span></label>
                                        <input type="text" name="code" id="code" value="{{ old('code', $standard->code) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    </div>

                                    <!-- Judul/Nama -->
                                    <div class="md:col-span-2">
                                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Judul') }} <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" id="name" value="{{ old('name', $standard->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    </div>

                                    <!-- Deskripsi -->
                                    <div class="md:col-span-2">
                                        <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Deskripsi') }}</label>
                                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $standard->description) }}</textarea>
                                    </div>

                                    <!-- Maksud dan Tujuan -->
                                    <div class="md:col-span-2">
                                        <label for="purpose" class="block text-sm font-medium text-gray-700">{{ __('Maksud dan Tujuan') }}</label>
                                        <textarea name="purpose" id="purpose" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('purpose', $standard->purpose) }}</textarea>
                                    </div>

                                    <!-- Urutan -->
                                    <div>
                                        <label for="order" class="block text-sm font-medium text-gray-700">{{ __('Urutan') }}</label>
                                        <input type="number" name="order" id="order" value="{{ old('order', $standard->order) }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <div class="flex items-start mt-6">
                                            <div class="flex items-center h-5">
                                                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $standard->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="is_active" class="font-medium text-gray-700">{{ __('Aktif') }}</label>
                                                <p class="text-gray-500">{{ __('Standar yang tidak aktif tidak akan ditampilkan di daftar.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button type="button" onclick="window.location='{{ route('snars.standards.show', $standard) }}'" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2">
                                        {{ __('Batal') }}
                                    </button>
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        {{ __('Simpan Perubahan') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chapterSelect = document.getElementById('chapter_id');
        
        // Cek apakah ada input hidden group_id
        let groupIdInput = document.querySelector('input[name="group_id"]');
        if (!groupIdInput) {
            // Jika tidak ada, buat input hidden baru
            groupIdInput = document.createElement('input');
            groupIdInput.type = 'hidden';
            groupIdInput.name = 'group_id';
            
            // Ambil group_id dari chapter awal
            const initialChapterId = chapterSelect.value;
            if (initialChapterId) {
                fetch(`/api/chapters/${initialChapterId}`)
                    .then(response => response.json())
                    .then(data => {
                        groupIdInput.value = data.group_id;
                        console.log('Group ID awal:', data.group_id);
                    })
                    .catch(error => console.error('Error:', error));
            }
            
            chapterSelect.form.appendChild(groupIdInput);
        }
        
        // Fungsi untuk mendapatkan group_id dari chapter yang dipilih
        function updateGroupId() {
            const chapterId = chapterSelect.value;
            if (!chapterId) return;
            
            // Ambil data group dari server
            fetch(`/api/chapters/${chapterId}`)
                .then(response => response.json())
                .then(data => {
                    groupIdInput.value = data.group_id;
                    console.log('Group ID diperbarui:', data.group_id);
                })
                .catch(error => console.error('Error:', error));
        }
        
        // Panggil saat chapter berubah
        if (chapterSelect) {
            chapterSelect.addEventListener('change', updateGroupId);
        }
    });
</script>
</html> 