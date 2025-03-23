<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Izin Modul') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">{{ __('Edit Izin untuk Role dan Modul') }}</h3>
                            <a href="{{ route('superadmin.module-permissions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 active:bg-gray-300 dark:active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                {{ __('Kembali') }}
                            </a>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                        <div class="mb-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300">{{ __('Detail Izin') }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Role: ') }} <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $role->name }}</span>
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Modul: ') }} <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $module->name }} ({{ $module->code }})</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('superadmin.module-permissions.update', ['roleId' => $role->id, 'moduleId' => $module->id]) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-md shadow-sm">
                                    <h5 class="font-semibold mb-4 text-gray-700 dark:text-gray-300">{{ __('Izin Akses') }}</h5>
                                    
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <input id="can_view" name="can_view" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" 
                                                {{ isset($permission->can_view) && $permission->can_view ? 'checked' : '' }}>
                                            <label for="can_view" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                                {{ __('Dapat Melihat') }}
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Pengguna dapat mengakses dan melihat konten modul ini') }}</p>
                                            </label>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <input id="can_create" name="can_create" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" 
                                                {{ isset($permission->can_create) && $permission->can_create ? 'checked' : '' }}>
                                            <label for="can_create" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                                {{ __('Dapat Membuat') }}
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Pengguna dapat membuat konten baru dalam modul ini') }}</p>
                                            </label>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <input id="can_edit" name="can_edit" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" 
                                                {{ isset($permission->can_edit) && $permission->can_edit ? 'checked' : '' }}>
                                            <label for="can_edit" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                                {{ __('Dapat Mengedit') }}
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Pengguna dapat mengedit konten yang ada dalam modul ini') }}</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-md shadow-sm">
                                    <h5 class="font-semibold mb-4 text-gray-700 dark:text-gray-300">{{ __('Izin Lanjutan') }}</h5>
                                    
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <input id="can_delete" name="can_delete" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" 
                                                {{ isset($permission->can_delete) && $permission->can_delete ? 'checked' : '' }}>
                                            <label for="can_delete" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                                {{ __('Dapat Menghapus') }}
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Pengguna dapat menghapus konten dalam modul ini') }}</p>
                                            </label>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <input id="can_approve" name="can_approve" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" 
                                                {{ isset($permission->can_approve) && $permission->can_approve ? 'checked' : '' }}>
                                            <label for="can_approve" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                                {{ __('Dapat Menyetujui') }}
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Pengguna dapat menyetujui permintaan/perubahan dalam modul ini') }}</p>
                                            </label>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <input id="can_activate" name="can_activate" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" 
                                                {{ isset($permission->can_activate) && $permission->can_activate ? 'checked' : '' }}>
                                            <label for="can_activate" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                                {{ __('Dapat Mengaktifkan') }}
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Pengguna dapat mengaktifkan/menonaktifkan modul ini') }}</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ __('Simpan Perubahan') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 