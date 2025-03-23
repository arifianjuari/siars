<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar Modul') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($modules->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak Ada Modul</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada modul yang tersedia saat ini.
                            </p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($modules as $module)
                                <div class="border dark:border-gray-700 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                    <div class="p-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700 flex justify-between items-center">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $module->name }}</h3>
                                        <span class="px-2 py-1 text-xs rounded-full
                                        @if($module->is_core)
                                            bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                        @else
                                            bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300
                                        @endif
                                        ">
                                            {{ $module->is_core ? 'Inti' : 'Opsional' }}
                                        </span>
                                    </div>
                                    
                                    <div class="p-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                                            {{ $module->description ?? 'Tidak ada deskripsi' }}
                                        </p>
                                        
                                        <div class="flex justify-between items-center">
                                            <!-- Status Modul -->
                                            @if(Auth::user()->tenant_id)
                                                <span class="px-2 py-1 text-xs rounded-full
                                                @if(isset($module->is_active_for_tenant) && $module->is_active_for_tenant)
                                                    bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                @else
                                                    bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                @endif
                                                ">
                                                    {{ (isset($module->is_active_for_tenant) && $module->is_active_for_tenant) ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            @endif
                                            
                                            <!-- Tombol -->
                                            <div class="flex space-x-2">
                                                @if($isTenantAdmin && Auth::user()->tenant_id && !$module->is_active_for_tenant)
                                                    <form action="{{ route('tenant.modules.request-activation') }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <input type="hidden" name="module_id" value="{{ $module->id }}">
                                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                            Ajukan Aktivasi
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('modules.show', $module->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 