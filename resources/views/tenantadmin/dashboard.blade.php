<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Admin Rumah Sakit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Selamat Datang di Dashboard Admin Rumah Sakit') }}</h3>
                    <p class="mb-4">{{ __('Anda memiliki akses penuh untuk mengelola pengguna dan modul dalam rumah sakit') }} <strong>{{ auth()->user()->tenant->name }}</strong>.</p>
                    <p>{{ __('Gunakan menu di bawah untuk mengakses fungsi-fungsi administratif rumah sakit.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- User Management Card -->
                <div class="bg-indigo-50 dark:bg-indigo-900 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="rounded-full bg-indigo-500 p-3 mr-4">
                                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Manajemen Pengguna') }}</h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">{{ __('Kelola pengguna rumah sakit, atur hak akses, dan perbarui informasi pengguna.') }}</p>
                        <a href="{{ route('tenantadmin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:bg-indigo-700 dark:focus:bg-indigo-600 active:bg-indigo-800 dark:active:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('KELOLA PENGGUNA') }}
                        </a>
                    </div>
                </div>

                <!-- Module Management Card -->
                <div class="bg-amber-50 dark:bg-amber-900 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="rounded-full bg-amber-500 p-3 mr-4">
                                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Manajemen Modul') }}</h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">{{ __('Lihat modul yang tersedia, ajukan permintaan aktivasi modul, dan pantau status permintaan.') }}</p>
                        <a href="{{ route('tenantadmin.modules') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 dark:bg-amber-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 dark:hover:bg-amber-600 focus:bg-amber-700 dark:focus:bg-amber-600 active:bg-amber-800 dark:active:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('KELOLA MODUL') }}
                        </a>
                    </div>
                </div>

                <!-- Hospital Profile Card -->
                <div class="bg-green-50 dark:bg-green-900 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="rounded-full bg-green-500 p-3 mr-4">
                                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Profil Rumah Sakit') }}</h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">{{ __('Lihat dan perbarui informasi rumah sakit, termasuk alamat, kontak, dan data lainnya.') }}</p>
                        <a href="{{ route('tenantadmin.hospital.profile') }}" class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 focus:bg-green-700 dark:focus:bg-green-600 active:bg-green-800 dark:active:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('LIHAT PROFIL') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Activated Modules Overview -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">{{ __('Modul Aktif') }}</h3>
                    
                    @if(count($activatedModules) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($activatedModules as $module)
                                <a href="{{ route('module.' . Str::slug($module->name)) }}" class="block bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150">
                                    <div class="flex items-center mb-2">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-500 dark:bg-green-800 dark:text-green-100 mr-2">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </span>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $module->name }}</h4>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($module->description, 60) }}</p>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 dark:text-gray-400">{{ __('Belum ada modul yang teraktivasi untuk rumah sakit Anda.') }}</p>
                            <a href="{{ route('tenantadmin.modules') }}" class="mt-2 inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                {{ __('Lihat modul tersedia') }}
                                <svg class="ml-1 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
