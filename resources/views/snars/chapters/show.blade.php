<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Detail Bab SNARS</title>

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
                    <a href="{{ route('snars.groups.show', $chapter->group) }}" class="hover:underline">{{ $chapter->group->name }}</a>
                    <span class="mx-1">/</span>
                    <span class="text-gray-700">{{ $chapter->code }}</span>
                </div>
            
                <div class="flex justify-between items-center">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Detail Bab SNARS') }}
                    </h2>
                    <div>
                        <a href="{{ route('snars.groups.show', $chapter->group) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Kembali ke Kelompok') }}
                        </a>
                        @if(!auth()->user()->hasRole(['Staf', 'ManajemenOperasional', 'ManajemenStrategis']))
                        <a href="{{ route('snars.chapters.edit', $chapter) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Edit Bab') }}
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
                            <!-- Chapter Details -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Informasi Bab') }}</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <div class="mb-4">
                                            <h4 class="text-sm font-medium text-gray-500">{{ __('Kode') }}</h4>
                                            <p class="mt-1 text-sm text-gray-900">{{ $chapter->code }}</p>
                                        </div>
                                        <div class="mb-4">
                                            <h4 class="text-sm font-medium text-gray-500">{{ __('Judul') }}</h4>
                                            <p class="mt-1 text-sm text-gray-900">{{ $chapter->title }}</p>
                                        </div>
                                        <div class="mb-4">
                                            <h4 class="text-sm font-medium text-gray-500">{{ __('Kelompok') }}</h4>
                                            <p class="mt-1 text-sm text-gray-900">{{ $chapter->group->code }} - {{ $chapter->group->name }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="mb-4">
                                            <h4 class="text-sm font-medium text-gray-500">{{ __('Urutan') }}</h4>
                                            <p class="mt-1 text-sm text-gray-900">{{ $chapter->order }}</p>
                                        </div>
                                        <div class="mb-4">
                                            <h4 class="text-sm font-medium text-gray-500">{{ __('Status') }}</h4>
                                            <p class="mt-1 text-sm text-gray-900">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $chapter->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $chapter->is_active ? __('Aktif') : __('Tidak Aktif') }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="mb-4">
                                            <h4 class="text-sm font-medium text-gray-500">{{ __('Dibuat Pada') }}</h4>
                                            <p class="mt-1 text-sm text-gray-900">{{ $chapter->created_at->format('d M Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-500">{{ __('Deskripsi') }}</h4>
                                    <p class="mt-1 text-sm text-gray-900">{{ $chapter->description ?? __('Tidak ada deskripsi.') }}</p>
                                </div>
                            </div>

                            <!-- Standards List -->
                            <div class="mt-8">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ __('Daftar Standar') }}</h3>
                                    @if(!auth()->user()->hasRole(['Staf', 'ManajemenOperasional', 'ManajemenStrategis']))
                                    <a href="{{ route('snars.standards.create', ['chapter_id' => $chapter->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Tambah Standar') }}
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
                                                    {{ __('Jumlah Elemen') }}
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
                                            @forelse ($chapter->standards as $standard)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $standard->code }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $standard->name }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $standard->elements_count ?? ($standard->elements ? $standard->elements->count() : 0) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $standard->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ $standard->is_active ? __('Aktif') : __('Tidak Aktif') }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <a href="{{ route('snars.standards.show', $standard) }}" class="text-blue-600 hover:text-blue-900 mr-2">{{ __('Detail') }}</a>
                                                        @if(!auth()->user()->hasRole(['Staf', 'ManajemenOperasional', 'ManajemenStrategis']))
                                                        <a href="{{ route('snars.standards.edit', $standard) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">{{ __('Edit') }}</a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                        {{ __('Tidak ada data standar untuk bab ini.') }}
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
