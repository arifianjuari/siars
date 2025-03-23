<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Tenant Selection for Superadmin -->
            @if(Auth::user()->hasRole('Superadmin'))
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <a href="{{ route('superadmin.tenants.index') }}" class="block p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 p-3 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h4 class="ml-4 text-lg font-medium text-gray-900 dark:text-gray-100">Manajemen Rumah Sakit</h4>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">Pilih dan kelola rumah sakit serta modul-modulnya</p>
                    <div class="mt-4 flex justify-end">
                        <span class="text-purple-600 dark:text-purple-400 inline-flex items-center text-sm font-medium">
                            Kelola Rumah Sakit
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </a>
            </div>

            <!-- User Management (for Superadmin) -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <a href="{{ route('superadmin.users.index') }}" class="block p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 bg-indigo-100 dark:bg-indigo-900 p-3 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h4 class="ml-4 text-lg font-medium text-gray-900 dark:text-gray-100">Manajemen Pengguna</h4>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">Kelola semua pengguna di seluruh rumah sakit</p>
                    <div class="mt-4 flex justify-end">
                        <span class="text-indigo-600 dark:text-indigo-400 inline-flex items-center text-sm font-medium">
                            Daftar Pengguna
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </a>
            </div>

            <!-- Module Administration -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 p-3 rounded-md">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        <h4 class="ml-4 text-lg font-medium text-gray-900 dark:text-gray-100">Manajemen Modul</h4>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                        <a href="{{ route('superadmin.modules.index') }}" class="block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Daftar Modul</h5>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Kelola modul sistem & rumah sakit</p>
                        </a>
                        
                        <a href="{{ route('superadmin.modules.requests') }}" class="block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Permintaan Aktivasi</h5>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Proses permintaan aktivasi modul</p>
                        </a>
                        
                        <a href="{{ route('superadmin.module-permissions.index') }}" class="block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Izin Modul</h5>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Kelola izin role untuk setiap modul</p>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- SNARS Module Card (if active for non-superadmin tenant) -->
            @if(!Auth::user()->hasRole('Superadmin') && Auth::user()->tenant_id)
                @php
                    $tenant = Auth::user()->tenant;
                    
                    // Check if SNARS module is active
                    $snarsModule = App\Models\Module::where('code', 'SNARS')->first();
                    $isSnarsActive = $snarsModule && App\Models\TenantModule::where('tenant_id', Auth::user()->tenant_id)
                        ->where('module_id', $snarsModule->id)
                        ->where('is_active', true)
                        ->whereNotNull('approved_by')
                        ->whereNotNull('approved_at')
                        ->exists();
                        
                    // Check if Risk Management module is active
                    $riskModule = App\Models\Module::where('name', 'Manajemen Risiko')->first();
                    $isRiskManagementActive = $riskModule && App\Models\TenantModule::where('tenant_id', Auth::user()->tenant_id)
                        ->where('module_id', $riskModule->id)
                        ->where('is_active', true)
                        ->whereNotNull('approved_by')
                        ->whereNotNull('approved_at')
                        ->exists();
                @endphp

                @if($isSnarsActive)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 p-3 rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h4 class="ml-4 text-lg font-medium text-gray-900 dark:text-gray-100">Modul SNARS</h4>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Akses dokumen pendukung SNARS</p>
                        <a href="/snars/dashboard" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Akses Modul SNARS
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                @endif

                @if($isRiskManagementActive)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 p-3 rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h4 class="ml-4 text-lg font-medium text-gray-900 dark:text-gray-100">Manajemen Risiko</h4>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Akses modul manajemen risiko rumah sakit</p>
                        <a href="/risk-management/dashboard" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Akses Modul Manajemen Risiko
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                @endif
            @endif

            <!-- Tenant Admin - Request Module Activation -->
            @if(Auth::user()->hasRole('TenantAdmin'))
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 bg-orange-100 dark:bg-orange-900 p-3 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600 dark:text-orange-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="ml-4 text-lg font-medium text-gray-900 dark:text-gray-100">Permintaan Aktivasi Modul</h4>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Ajukan permintaan untuk mengaktifkan modul</p>
                    <a href="{{ route('modules.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Lihat Modul Tersedia
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
            @endif

            <!-- Greeting Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Selamat datang, {{ Auth::user()->name }}!</h2>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ now()->format('l, d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Semua Modul Aktif untuk Non-Superadmin -->
            @if(!Auth::user()->hasRole('Superadmin') && Auth::user()->tenant_id)
                @php
                    $activatedModules = Auth::user()->tenant->modules()
                        ->wherePivot('is_active', true)
                        ->whereNotNull('approved_by')
                        ->whereNotNull('approved_at')
                        ->get();
                @endphp

                @if(count($activatedModules) > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Modul Aktif</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($activatedModules as $module)
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <div class="flex items-center mb-2">
                                        <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 p-3 rounded-md mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">Modul {{ $module->name }}</h4>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $module->description }}</p>
                                    
                                    @if($module->name == 'SNARS')
                                        <a href="/snars/dashboard" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                            Akses Modul SNARS
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @elseif($module->name == 'Manajemen Risiko')
                                        <a href="/risk-management/dashboard" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                            Akses Manajemen Risiko
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @else
                                        <a href="{{ route('modules.show', $module->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                            Lihat Detail Modul
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            @endif

            <!-- Notification for module activation requests -->
            @if(auth()->check())
                <div class="mb-4">
                    <x-activation-request-notification />
                </div>
            @endif
        </div>
    </div>

    <!-- TenantAdmin Section -->
    @if(Auth::user()->hasRole('TenantAdmin'))
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                {{ __('Administrasi Rumah Sakit') }}
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- User Management Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-amber-100 dark:bg-amber-900 rounded-full">
                                <svg class="h-8 w-8 text-amber-600 dark:text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                                    {{ __('Manajemen Pengguna') }}
                                </h2>
                                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Kelola pengguna di rumah sakit Anda') }}</p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('tenantadmin.users.index') }}" class="uppercase text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-md inline-block transition">
                                {{ __('KELOLA PENGGUNA') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Module Management Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-amber-100 dark:bg-amber-900 rounded-full">
                                <svg class="h-8 w-8 text-amber-600 dark:text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                                    {{ __('Manajemen Modul') }}
                                </h2>
                                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Kelola dan aktifkan modul untuk rumah sakit Anda') }}</p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('tenantadmin.modules') }}" class="uppercase text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-md inline-block transition">
                                {{ __('KELOLA MODUL') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modul Aktif Section -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">{{ __('Modul Aktif') }}</h3>
                    
                    @php
                        $activatedModules = Auth::user()->tenant->modules()->wherePivot('is_active', true)->get();
                    @endphp
                    
                    @if(count($activatedModules) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($activatedModules as $module)
                                <a href="{{ route('modules.show', $module->id) }}" class="block bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150">
                                    <div class="flex items-center mb-2">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-500 dark:bg-green-800 dark:text-green-100 mr-2">
                                            <i class="{{ $module->icon }}"></i>
                                        </span>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $module->name }}</h4>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $module->description }}</p>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 dark:text-gray-400">{{ __('Belum ada modul yang aktif untuk rumah sakit Anda.') }}</p>
                            <a href="{{ route('tenantadmin.modules') }}" class="mt-2 inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                {{ __('Kelola modul') }}
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
    @endif
</x-app-layout>
