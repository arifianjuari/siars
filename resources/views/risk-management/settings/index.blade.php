<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengaturan Manajemen Risiko') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-6">{{ __('Pengaturan Sistem') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Kategori Risiko -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition">
                            <div class="flex items-center mb-4">
                                <div class="rounded-full bg-blue-100 dark:bg-blue-900 p-3 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-medium">Kategori Risiko</h4>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">Kelola kategori dan subkategori risiko yang digunakan dalam sistem.</p>
                            <a href="{{ route('risk-management.settings.categories') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Kelola Kategori
                            </a>
                        </div>

                        <!-- Faktor Penyebab -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition">
                            <div class="flex items-center mb-4">
                                <div class="rounded-full bg-red-100 dark:bg-red-900 p-3 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-medium">Faktor Penyebab</h4>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">Kelola faktor-faktor penyebab risiko dan insiden.</p>
                            <a href="{{ route('risk-management.settings.factors') }}" class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:bg-red-700 dark:focus:bg-red-600 active:bg-red-900 dark:active:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Kelola Faktor
                            </a>
                        </div>

                        <!-- Lokasi -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition">
                            <div class="flex items-center mb-4">
                                <div class="rounded-full bg-green-100 dark:bg-green-900 p-3 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-medium">Lokasi</h4>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">Kelola lokasi atau area di mana insiden dapat terjadi.</p>
                            <a href="{{ route('risk-management.settings.locations') }}" class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 focus:bg-green-700 dark:focus:bg-green-600 active:bg-green-900 dark:active:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Kelola Lokasi
                            </a>
                        </div>

                        <!-- Jenis Insiden -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition">
                            <div class="flex items-center mb-4">
                                <div class="rounded-full bg-purple-100 dark:bg-purple-900 p-3 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-medium">Jenis Insiden</h4>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">Kelola jenis dan subtipe insiden yang dilaporkan.</p>
                            <a href="{{ route('risk-management.settings.incident-types') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 dark:bg-purple-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 dark:hover:bg-purple-600 focus:bg-purple-700 dark:focus:bg-purple-600 active:bg-purple-900 dark:active:bg-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Kelola Jenis Insiden
                            </a>
                        </div>

                        <!-- Matriks Risiko -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition">
                            <div class="flex items-center mb-4">
                                <div class="rounded-full bg-yellow-100 dark:bg-yellow-900 p-3 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 dark:text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-medium">Matriks Risiko</h4>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">Konfigurasi matriks risiko untuk penilaian dampak dan probabilitas.</p>
                            <a href="{{ route('risk-management.settings.risk-matrix') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 dark:bg-yellow-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 dark:hover:bg-yellow-600 focus:bg-yellow-700 dark:focus:bg-yellow-600 active:bg-yellow-900 dark:active:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Kelola Matriks
                            </a>
                        </div>

                        <!-- Level Risiko -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition">
                            <div class="flex items-center mb-4">
                                <div class="rounded-full bg-indigo-100 dark:bg-indigo-900 p-3 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-medium">Level Risiko</h4>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">Konfigurasi level risiko dan tindakan yang disarankan.</p>
                            <a href="{{ route('risk-management.settings.risk-levels') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:bg-indigo-700 dark:focus:bg-indigo-600 active:bg-indigo-900 dark:active:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Kelola Level
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 