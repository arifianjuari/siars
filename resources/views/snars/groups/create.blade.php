<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Tambah Kelompok SNARS</title>

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
                    <span class="text-gray-700">Tambah</span>
                </div>

                <div class="flex justify-between items-center">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Tambah Kelompok SNARS Baru') }}
                    </h2>
                    <div>
                        <a href="{{ route('snars.groups.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Kembali ke Daftar') }}
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
                            <form method="POST" action="{{ route('snars.groups.store') }}">
                                @csrf

                                <!-- Validation Errors -->
                                @if ($errors->any())
                                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-md">
                                        <div class="font-medium">{{ __('Oops! Ada beberapa masalah dengan input Anda.') }}</div>
                                        <ul class="mt-3 list-disc list-inside text-sm">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Group Information -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Informasi Kelompok') }}</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Code -->
                                        <div>
                                            <label for="code" class="block font-medium text-sm text-gray-700">{{ __('Kode') }}</label>
                                            <input id="code" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" type="text" name="code" value="{{ old('code') }}" required autofocus />
                                            <p class="mt-1 text-xs text-gray-500">{{ __('Contoh: K1, K2, dst.') }}</p>
                                        </div>

                                        <!-- Name -->
                                        <div>
                                            <label for="name" class="block font-medium text-sm text-gray-700">{{ __('Nama') }}</label>
                                            <input id="name" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" type="text" name="name" value="{{ old('name') }}" required />
                                        </div>

                                        <!-- Order -->
                                        <div>
                                            <label for="order" class="block font-medium text-sm text-gray-700">{{ __('Urutan') }}</label>
                                            <input id="order" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" type="number" name="order" value="{{ old('order') }}" min="1" />
                                            <p class="mt-1 text-xs text-gray-500">{{ __('Urutan tampilan kelompok. Biarkan kosong untuk otomatis.') }}</p>
                                        </div>

                                        <!-- Status -->
                                        <div>
                                            <label for="is_active" class="block font-medium text-sm text-gray-700">{{ __('Status') }}</label>
                                            <select id="is_active" name="is_active" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>{{ __('Aktif') }}</option>
                                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>{{ __('Tidak Aktif') }}</option>
                                            </select>
                                        </div>

                                        <!-- Version ID -->
                                        <div>
                                            <label for="version_id" class="block font-medium text-sm text-gray-700">{{ __('Versi SNARS') }}</label>
                                            <select id="version_id" name="version_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                                @foreach($versions as $version)
                                                    <option value="{{ $version->id }}" {{ old('version_id') == $version->id ? 'selected' : '' }}>
                                                        {{ $version->name }} ({{ $version->version_number }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="mt-4">
                                        <label for="description" class="block font-medium text-sm text-gray-700">{{ __('Deskripsi') }}</label>
                                        <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex justify-end mt-6">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Simpan Kelompok') }}
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
