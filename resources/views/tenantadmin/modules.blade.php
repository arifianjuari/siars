<div>
    <!-- Life is available only in the present moment. - Thich Nhat Hanh -->
</div>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Modul Rumah Sakit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Activated Modules -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Modul Teraktivasi') }}
                            </div>
                        </h3>

                        @if(count($activatedModules) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach($activatedModules as $module)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                                        <div class="flex items-center mb-2">
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-500 dark:bg-green-800 dark:text-green-100 mr-2">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </span>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $module->name }}</h4>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ $module->description }}</p>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-medium text-green-600 dark:text-green-400">Aktif</span>
                                            <a href="{{ route('tenantadmin.modules.view', $module->id) }}" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-gray-500 dark:text-gray-400">{{ __('Belum ada modul yang teraktivasi untuk rumah sakit Anda.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Available Modules -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                {{ __('Modul Tersedia') }}
                            </div>
                        </h3>

                        @if(count($availableModules) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach($availableModules as $module)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                                        <div class="flex items-center mb-2">
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-200 text-gray-500 dark:bg-gray-600 dark:text-gray-300 mr-2">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                            </span>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $module->name }}</h4>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ $module->description }}</p>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Tidak Aktif</span>
                                            <form action="{{ route('tenantadmin.modules.request-activation', $module->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-xs text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300">
                                                    Ajukan Aktivasi
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-gray-500 dark:text-gray-400">{{ __('Semua modul sudah teraktivasi atau tidak ada modul baru yang tersedia.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pending Activation Requests -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('Permintaan Aktivasi Tertunda') }}
                        </div>
                    </h3>

                    @if(count($pendingRequests) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Modul') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Tanggal Permintaan') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Status') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Aksi') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    @foreach($pendingRequests as $request)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $request->module->name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($request->module->description, 50) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $request->created_at->format('d M Y') }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $request->created_at->format('H:i') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                                    {{ __('Menunggu Persetujuan') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <form action="{{ route('tenantadmin.modules.cancel-request', $request->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('Anda yakin ingin membatalkan permintaan ini?')">
                                                        {{ __('Batalkan') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 dark:text-gray-400">{{ __('Tidak ada permintaan aktivasi yang tertunda.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
