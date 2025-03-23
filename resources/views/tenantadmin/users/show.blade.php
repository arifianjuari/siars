<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail Pengguna') }}: {{ $user->name }}
            </h2>
            <div class="flex items-center space-x-4">
                <a href="{{ route('tenantadmin.users.edit', $user->id) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 dark:bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 dark:hover:bg-amber-700 focus:bg-amber-600 dark:focus:bg-amber-700 active:bg-amber-700 dark:active:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('tenantadmin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-800 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    {{ __('Kembali') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Berhasil!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-600">
                                {{ __('Informasi Dasar') }}
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nama') }}</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Email') }}</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Status') }}</p>
                                    <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                        {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tanggal Bergabung') }}</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->created_at->format('d F Y') }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Terakhir Diperbarui') }}</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->updated_at->format('d F Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-600">
                                {{ __('Informasi Profesional') }}
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Role') }}</p>
                                    <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                        {{ $user->roles->first()->name ?? 'Tanpa Role' }}
                                    </span>
                                </div>
                                
                                @if($user->employee_id)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('ID Karyawan') }}</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->employee_id }}</p>
                                </div>
                                @endif
                                
                                @if($user->position)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Jabatan') }}</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->position }}</p>
                                </div>
                                @endif
                                
                                @if($user->department)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Departemen') }}</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->department->name }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($user->additional_info)
                    <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-600">
                            {{ __('Informasi Tambahan') }}
                        </h3>
                        <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $user->additional_info }}</p>
                    </div>
                    @endif

                    <div class="mt-6 flex justify-between items-center">
                        <div class="flex space-x-4">
                            <form action="{{ route('tenantadmin.users.toggle-active', $user->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-{{ $user->is_active ? 'orange' : 'green' }}-500 dark:bg-{{ $user->is_active ? 'orange' : 'green' }}-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-{{ $user->is_active ? 'orange' : 'green' }}-600 dark:hover:bg-{{ $user->is_active ? 'orange' : 'green' }}-700 focus:bg-{{ $user->is_active ? 'orange' : 'green' }}-600 dark:focus:bg-{{ $user->is_active ? 'orange' : 'green' }}-700 active:bg-{{ $user->is_active ? 'orange' : 'green' }}-700 dark:active:bg-{{ $user->is_active ? 'orange' : 'green' }}-800 focus:outline-none focus:ring-2 focus:ring-{{ $user->is_active ? 'orange' : 'green' }}-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ $user->is_active ? __('Nonaktifkan Pengguna') : __('Aktifkan Pengguna') }}
                                </button>
                            </form>
                        </div>
                        
                        <form action="{{ route('tenantadmin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:bg-red-700 dark:focus:bg-red-600 active:bg-red-800 dark:active:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Hapus Pengguna') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
