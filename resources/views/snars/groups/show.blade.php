<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Detail Kelompok SNARS</title>

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
                    <span class="text-gray-700">{{ $group->name }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Detail Kelompok SNARS') }}
                    </h2>
                    <div>
                        <a href="{{ route('snars.groups.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Kembali ke Daftar') }}
                        </a>
                        @if(!auth()->user()->hasRole(['Staf', 'ManajemenOperasional', 'ManajemenStrategis']))
                        <a href="{{ route('snars.groups.edit', $group) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Edit Kelompok') }}
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
                            <!-- Group Details -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Informasi Kelompok') }}</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <div class="mb-4">
                                            <h4 class="text-sm font-medium text-gray-500">{{ __('Nama') }}</h4>
                                            <p class="mt-1 text-sm text-gray-900">{{ $group->name }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="mb-4">
                                            <h4 class="text-sm font-medium text-gray-500">{{ __('Versi SNARS') }}</h4>
                                            <p class="mt-1 text-sm text-gray-900">
                                                {{ $group->version->name ?? 'N/A' }} ({{ $group->version->version_number ?? '' }})
                                            </p>
                                        </div>
                                        <div class="mb-4">
                                            <h4 class="text-sm font-medium text-gray-500">{{ __('Dibuat Pada') }}</h4>
                                            <p class="mt-1 text-sm text-gray-900">{{ $group->created_at->format('d M Y H:i') }}</p>
                                        </div>
                                        <div class="mb-4">
                                            <h4 class="text-sm font-medium text-gray-500">{{ __('Diperbarui Pada') }}</h4>
                                            <p class="mt-1 text-sm text-gray-900">{{ $group->updated_at->format('d M Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-500">{{ __('Deskripsi') }}</h4>
                                    <p class="mt-1 text-sm text-gray-900">{{ $group->description ?? __('Tidak ada deskripsi.') }}</p>
                                </div>
                            </div>

                            <!-- Chapters List -->
                            <div class="mt-8">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ __('Daftar Bab') }}</h3>
                                    @if(!auth()->user()->hasRole(['Staf', 'ManajemenOperasional', 'ManajemenStrategis']))
                                    <a href="{{ route('snars.chapters.create', ['group_id' => $group->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Tambah Bab') }}
                                    </a>
                                    @endif
                                </div>
                                
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Kode') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Judul') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Jumlah Standar') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Status') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Aksi') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse ($group->chapters as $chapter)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $chapter->code }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $chapter->title }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $chapter->standards_count ?? $chapter->standards->count() }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $chapter->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ $chapter->is_active ? __('Aktif') : __('Tidak Aktif') }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <a href="{{ route('snars.chapters.show', $chapter) }}" class="text-blue-600 hover:text-blue-900 mr-2">{{ __('Detail') }}</a>
                                                        @if(!auth()->user()->hasRole(['Staf', 'ManajemenOperasional', 'ManajemenStrategis']))
                                                        <a href="{{ route('snars.chapters.edit', $chapter) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">{{ __('Edit') }}</a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                        {{ __('Tidak ada data bab untuk kelompok ini.') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
