<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Tambah Elemen Penilaian</title>

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
                <div class="flex justify-between items-center">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Tambah Elemen Penilaian') }}
                    </h2>
                    <div>
                        @if(request()->has('standard_id'))
                            <a href="{{ route('snars.standards.show', request('standard_id')) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Kembali ke Standar') }}
                            </a>
                        @else
                            <a href="{{ route('snars.assessment-elements.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Kembali ke Daftar') }}
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

                            <form action="{{ route('snars.assessment-elements.store') }}" method="POST">
                                @csrf
                                
                                <!-- Standard Selection -->
                                <div class="mb-6">
                                    <label for="standard_id" class="block text-sm font-medium text-gray-700">{{ __('Standar') }} <span class="text-red-600">*</span></label>
                                    <select id="standard_id" name="standard_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        <option value="">{{ __('Pilih Standar') }}</option>
                                        @foreach($standards as $standard)
                                            <option value="{{ $standard->id }}" {{ (old('standard_id') == $standard->id || request('standard_id') == $standard->id) ? 'selected' : '' }}>
                                                {{ $standard->code }} - {{ $standard->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Code -->
                                    <div class="mb-6">
                                        <label for="code" class="block text-sm font-medium text-gray-700">{{ __('Kode') }} <span class="text-red-600">*</span></label>
                                        <input type="text" name="code" id="code" value="{{ old('code') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    </div>
                                    
                                    <!-- Level -->
                                    <div class="mb-6">
                                        <label for="level" class="block text-sm font-medium text-gray-700">{{ __('Tingkat') }} <span class="text-red-600">*</span></label>
                                        <select id="level" name="level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                            <option value="">{{ __('Pilih Tingkat') }}</option>
                                            <option value="dasar" {{ old('level') == 'dasar' ? 'selected' : '' }}>{{ __('Dasar') }}</option>
                                            <option value="madya" {{ old('level') == 'madya' ? 'selected' : '' }}>{{ __('Madya') }}</option>
                                            <option value="utama" {{ old('level') == 'utama' ? 'selected' : '' }}>{{ __('Utama') }}</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Description -->
                                <div class="mb-6">
                                    <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Deskripsi Elemen Penilaian') }} <span class="text-red-600">*</span></label>
                                    <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>{{ old('description') }}</textarea>
                                </div>
                                
                                <!-- Verification Method -->
                                <div class="mb-6">
                                    <label for="verification_method" class="block text-sm font-medium text-gray-700">{{ __('Metode Verifikasi') }}</label>
                                    <textarea id="verification_method" name="verification_method" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('verification_method') }}</textarea>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Score Weight -->
                                    <div class="mb-6">
                                        <label for="score_weight" class="block text-sm font-medium text-gray-700">{{ __('Bobot Nilai') }}</label>
                                        <input type="number" name="score_weight" id="score_weight" value="{{ old('score_weight', 1) }}" min="0" step="0.1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    
                                    <!-- Is Active -->
                                    <div class="mb-6">
                                        <label for="is_active" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                                        <select id="is_active" name="is_active" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>{{ __('Aktif') }}</option>
                                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>{{ __('Tidak Aktif') }}</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Department Assignment -->
                                <div class="mb-6" x-data="{ showDepartments: false }">
                                    <div class="flex items-center mb-2">
                                        <label class="block text-sm font-medium text-gray-700 mr-2">{{ __('Departemen Terkait') }}</label>
                                        <button type="button" @click="showDepartments = !showDepartments" class="text-sm text-blue-600 hover:text-blue-800">
                                            <span x-show="!showDepartments">{{ __('Tampilkan') }}</span>
                                            <span x-show="showDepartments">{{ __('Sembunyikan') }}</span>
                                        </button>
                                    </div>
                                    
                                    <div x-show="showDepartments" class="border border-gray-200 rounded-md p-4 mt-2">
                                        <p class="text-sm text-gray-600 mb-3">{{ __('Pilih departemen yang bertanggung jawab untuk elemen penilaian ini:') }}</p>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-h-60 overflow-y-auto">
                                            @foreach($departments as $department)
                                                <div class="flex items-center">
                                                    <input id="department_{{ $department->id }}" name="departments[]" type="checkbox" value="{{ $department->id }}" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ in_array($department->id, old('departments', [])) ? 'checked' : '' }}>
                                                    <label for="department_{{ $department->id }}" class="ml-2 block text-sm text-gray-700">
                                                        {{ $department->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Notes -->
                                <div class="mb-6">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">{{ __('Catatan') }}</label>
                                    <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Simpan Elemen Penilaian') }}
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
</html>
