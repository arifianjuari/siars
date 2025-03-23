<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Manajemen Eksekutif') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- User Info Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium">Selamat datang, {{ Auth::user()->name }}!</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                    {{ Auth::user()->roles->pluck('name')->first() }}
                                </span>
                                <span class="ml-2">{{ Auth::user()->email }}</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->department }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->position }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Fitur Utama</h3>
                    
                    <!-- Modul Aktif -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        @if(isset($activatedModules) && $activatedModules->count() > 0)
                            <div class="col-span-1 md:col-span-2">
                                <h4 class="text-lg font-semibold mb-3">Modul Aktif</h4>
                            </div>
                            
                            @foreach($activatedModules as $module)
                                <div class="bg-{{ $module->code == 'SNARS' ? 'red' : ($module->code == 'RISK' ? 'green' : 'blue') }}-100 dark:bg-{{ $module->code == 'SNARS' ? 'red' : ($module->code == 'RISK' ? 'green' : 'blue') }}-900 p-4 rounded-lg shadow">
                                    <h4 class="font-bold">Modul {{ $module->name }}</h4>
                                    <p>{{ $module->description }}</p>
                                    
                                    @if($module->code == 'SNARS')
                                        <a href="/snars/dashboard" class="mt-2 inline-flex items-center text-sm text-red-600 dark:text-red-400 hover:underline">
                                            Akses Modul SNARS
                                            <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </a>
                                    @elseif($module->code == 'RISK')
                                        <a href="/risk-management/dashboard" class="mt-2 inline-flex items-center text-sm text-green-600 dark:text-green-400 hover:underline">
                                            Akses Modul Manajemen Risiko
                                            <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </a>
                                    @else
                                        <a href="{{ route('modules.show', $module->id) }}" class="mt-2 inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                            Lihat Detail Modul
                                            <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="col-span-1 md:col-span-2 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                                <p class="text-gray-600 dark:text-gray-300">Tidak ada modul aktif untuk rumah sakit Anda.</p>
                            </div>
                        @endif
                    </div>
                    
                    <h4 class="text-lg font-semibold mb-3">Menu Utama</h4>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
