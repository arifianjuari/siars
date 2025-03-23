<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Daftar Standar SNARS</title>

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
                        {{ __('Daftar Standar SNARS') }}
                    </h2>
                    <div>
                        <a href="{{ route('snars.standards.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Tambah Standar') }}
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <!-- Filter Form -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <form action="{{ route('snars.standards.index') }}" method="GET" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <!-- Search -->
                                    <div>
                                        <label for="search" class="block text-sm font-medium text-gray-700">{{ __('Cari') }}</label>
                                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ __('Kode atau nama standar...') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    
                                    <!-- Group Filter -->
                                    <div>
                                        <label for="group_id" class="block text-sm font-medium text-gray-700">{{ __('Kelompok') }}</label>
                                        <select id="group_id" name="group_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">{{ __('Semua Kelompok') }}</option>
                                            @foreach($groups as $group)
                                                <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                                    {{ $group->code }} - {{ $group->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Chapter Filter -->
                                    <div>
                                        <label for="chapter_id" class="block text-sm font-medium text-gray-700">{{ __('Bab') }}</label>
                                        <select id="chapter_id" name="chapter_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">{{ __('Semua Bab') }}</option>
                                            @foreach($chapters as $chapter)
                                                <option value="{{ $chapter->id }}" {{ request('chapter_id') == $chapter->id ? 'selected' : '' }}>
                                                    {{ $chapter->code }} - {{ $chapter->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Status Filter -->
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">{{ __('Semua Status') }}</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Aktif') }}</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Tidak Aktif') }}</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end">
                                    <a href="{{ route('snars.standards.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                        {{ __('Reset') }}
                                    </a>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Filter') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Standards Table -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Kode') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Nama Standar') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Bab') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Kelompok') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Elemen Penilaian') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Status') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Aksi') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" x-data="{ selectedStandard: null }">
                                        @forelse($standards as $standard)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $standard->code }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-800">
                                                    {{ $standard->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                    <a href="{{ route('snars.chapters.show', $standard->chapter) }}" class="text-blue-600 hover:text-blue-900">
                                                        {{ $standard->chapter->code }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                    <a href="{{ route('snars.groups.show', $standard->chapter->group) }}" class="text-blue-600 hover:text-blue-900">
                                                        {{ $standard->chapter->group->code }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                    {{ $standard->assessmentElements->count() }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $standard->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                        {{ $standard->is_active ? __('Aktif') : __('Tidak Aktif') }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="{{ route('snars.standards.show', $standard) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">{{ __('Detail') }}</a>
                                                    <a href="{{ route('snars.standards.edit', $standard) }}" class="text-blue-600 hover:text-blue-900 mr-3">{{ __('Edit') }}</a>
                                                    <button type="button" @click="selectedStandard = '{{ $standard->id }}'" class="text-red-600 hover:text-red-900">{{ __('Hapus') }}</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                    {{ __('Tidak ada standar yang tersedia.') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $standards->links() }}
                            </div>
                            
                            <!-- Delete Confirmation Modal -->
                            <div x-show="selectedStandard" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50" x-cloak>
                                <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <div class="sm:flex sm:items-start">
                                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </div>
                                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                                    {{ __('Hapus Standar') }}
                                                </h3>
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-500">
                                                        {{ __('Apakah Anda yakin ingin menghapus standar ini? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua elemen penilaian terkait.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                        <form :action="'{{ route('snars.standards.destroy', '') }}/' + selectedStandard" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>
                                        <button type="button" @click="selectedStandard = null" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                            {{ __('Batal') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Dynamic filtering for chapters based on selected group
        document.addEventListener('DOMContentLoaded', function() {
            const groupSelect = document.getElementById('group_id');
            const chapterSelect = document.getElementById('chapter_id');
            
            if (groupSelect && chapterSelect) {
                const originalChapters = Array.from(chapterSelect.options).map(option => ({
                    value: option.value,
                    text: option.text,
                    group_id: option.dataset.groupId
                }));
                
                groupSelect.addEventListener('change', function() {
                    const selectedGroupId = this.value;
                    
                    // Clear current options except the first one
                    while (chapterSelect.options.length > 1) {
                        chapterSelect.remove(1);
                    }
                    
                    // If no group is selected, show all chapters
                    if (!selectedGroupId) {
                        originalChapters.forEach(chapter => {
                            if (chapter.value) {
                                const option = new Option(chapter.text, chapter.value);
                                option.dataset.groupId = chapter.group_id;
                                chapterSelect.add(option);
                            }
                        });
                        return;
                    }
                    
                    // Filter chapters by selected group
                    const filteredChapters = originalChapters.filter(chapter => 
                        chapter.group_id === selectedGroupId || !chapter.value
                    );
                    
                    // Add filtered chapters to select
                    filteredChapters.forEach(chapter => {
                        if (chapter.value) {
                            const option = new Option(chapter.text, chapter.value);
                            option.dataset.groupId = chapter.group_id;
                            chapterSelect.add(option);
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
