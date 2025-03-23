<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Detail Standar SNARS</title>

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
                    <a href="{{ route('snars.groups.show', $standard->chapter->group) }}" class="hover:underline">{{ $standard->chapter->group->name }}</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('snars.chapters.show', $standard->chapter) }}" class="hover:underline">{{ $standard->chapter->name }}</a>
                    <span class="mx-1">/</span>
                    <span class="text-gray-700">{{ $standard->code }}</span>
                </div>
            
                <div class="flex justify-between items-center">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Detail Standar SNARS') }}
                    </h2>
                    <div>
                        <a href="{{ route('snars.chapters.show', $standard->chapter) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Kembali ke Bab') }}
                        </a>
                        @if(!auth()->user()->hasRole('Staf'))
                        <a href="{{ route('snars.standards.edit', $standard) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Edit Standar') }}
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
                            <!-- Standard Details -->
                            <div class="mb-8">
                                <div class="flex items-center mb-4">
                                    <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-md mr-4">
                                        {{ $standard->code }}
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ $standard->name }}</h3>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">{{ __('Bab') }}</h4>
                                        <p class="text-sm text-gray-800">
                                            <a href="{{ route('snars.chapters.show', $standard->chapter) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $standard->chapter->code }} - {{ $standard->chapter->name }}
                                            </a>
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">{{ __('Kelompok') }}</h4>
                                        <p class="text-sm text-gray-800">
                                            <a href="{{ route('snars.groups.show', $standard->chapter->group) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $standard->chapter->group->code }} - {{ $standard->chapter->group->name }}
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">{{ __('Maksud dan Tujuan') }}</h4>
                                    <div class="prose max-w-none text-sm text-gray-800">
                                        {!! $standard->purpose !!}
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">{{ __('Deskripsi') }}</h4>
                                    <div class="prose max-w-none text-sm text-gray-800">
                                        {!! $standard->description !!}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Assessment Elements Section -->
                            <div class="mt-8">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ __('Elemen Penilaian') }}</h3>
                                    @if(!auth()->user()->hasRole('Staf'))
                                    <a href="{{ route('snars.assessment-elements.create', ['standard_id' => $standard->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Tambah Elemen Penilaian') }}
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
                                                    {{ __('Elemen Penilaian') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Tingkat') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Dokumen Pendukung') }}
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
                                            @forelse($standard->assessmentElements as $element)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $element->code }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-800">
                                                        {{ $element->description }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $element->level === 'dasar' ? 'bg-green-100 text-green-800' : ($element->level === 'madya' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                            {{ ucfirst($element->level) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        {{ $element->supportingDocuments->count() }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $element->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                            {{ $element->is_active ? __('Aktif') : __('Tidak Aktif') }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <a href="{{ route('snars.assessment-elements.show', $element) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">{{ __('Detail') }}</a>
                                                        @if(!auth()->user()->hasRole('Staf'))
                                                        <a href="{{ route('snars.assessment-elements.edit', $element) }}" class="text-blue-600 hover:text-blue-900 mr-3">{{ __('Edit') }}</a>
                                                        <form action="{{ route('snars.assessment-elements.destroy', $element) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('Apakah Anda yakin ingin menghapus elemen penilaian ini?') }}')">{{ __('Hapus') }}</button>
                                                        </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                        {{ __('Tidak ada elemen penilaian yang tersedia.') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Monitoring Section -->
                            <div class="mt-8">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ __('Monitoring Kepatuhan') }}</h3>
                                    <a href="{{ route('snars.monitoring-schedules.create', ['standard_id' => $standard->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Tambah Jadwal Monitoring') }}
                                    </a>
                                </div>
                                
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                                    <div class="p-4 bg-blue-50 border-b border-blue-200">
                                        <h4 class="text-md font-medium text-blue-800">{{ __('Ringkasan Kepatuhan') }}</h4>
                                    </div>
                                    <div class="p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                                <div class="text-2xl font-bold text-green-700">{{ $complianceData['compliance_percentage'] ?? '0' }}%</div>
                                                <div class="text-sm text-green-600">{{ __('Tingkat Kepatuhan') }}</div>
                                            </div>
                                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                                <div class="text-2xl font-bold text-blue-700">{{ $complianceData['total_elements'] ?? '0' }}</div>
                                                <div class="text-sm text-blue-600">{{ __('Total Elemen') }}</div>
                                            </div>
                                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                                <div class="text-2xl font-bold text-green-700">{{ $complianceData['compliant_elements'] ?? '0' }}</div>
                                                <div class="text-sm text-green-600">{{ __('Elemen Patuh') }}</div>
                                            </div>
                                            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                                <div class="text-2xl font-bold text-red-700">{{ $complianceData['non_compliant_elements'] ?? '0' }}</div>
                                                <div class="text-sm text-red-600">{{ __('Elemen Tidak Patuh') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Departemen') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Elemen Penilaian') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Frekuensi') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Tanggal Mulai') }}
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
                                            @forelse($monitoringSchedules ?? [] as $schedule)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        {{ $schedule->department->name }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-800">
                                                        {{ $schedule->element->code }} - {{ Str::limit($schedule->element->description, 50) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        {{ ucfirst($schedule->frequency) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        {{ $schedule->start_date->format('d/m/Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $schedule->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                            {{ $schedule->is_active ? __('Aktif') : __('Tidak Aktif') }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <a href="{{ route('snars.monitoring-schedules.show', $schedule) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">{{ __('Detail') }}</a>
                                                        <a href="{{ route('snars.monitoring-results.create', ['schedule_id' => $schedule->id]) }}" class="text-green-600 hover:text-green-900 mr-3">{{ __('Catat Hasil') }}</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                        {{ __('Tidak ada jadwal monitoring yang tersedia.') }}
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
