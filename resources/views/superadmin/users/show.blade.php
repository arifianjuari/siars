<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail Pengguna') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('superadmin.users.edit', $user->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                <a href="{{ route('superadmin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Kartu Utama -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row">
                        <!-- Avatar dan Informasi Status -->
                        <div class="w-full md:w-1/3 flex flex-col items-center mb-6 md:mb-0">
                            <div class="h-36 w-36 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 flex items-center justify-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                            
                            <!-- Status Pengguna -->
                            <div class="mt-4">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                            
                            <!-- Role Pengguna -->
                            <div class="mt-2">
                                @foreach($user->roles as $role)
                                    <span class="px-2 py-1 mt-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($role->name == 'Superadmin') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                        @elseif($role->name == 'ManajemenStrategis') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($role->name == 'ManajemenEksekutif') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($role->name == 'ManajemenOperasional') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                        @endif">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Informasi Detail -->
                        <div class="w-full md:w-2/3 md:pl-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 md:mt-0 mt-4">Informasi Pengguna</h3>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-4">
                                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">Informasi Akun</h4>
                                <dl class="grid grid-cols-1 gap-y-2 sm:grid-cols-2 sm:gap-x-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID Pegawai</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->employee_id ?? 'Tidak ada' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Terdaftar Pada</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->created_at->format('d M Y, H:i') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Terakhir Diperbarui</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->updated_at->format('d M Y, H:i') }}</dd>
                                    </div>
                                </dl>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-4">
                                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">Informasi Rumah Sakit</h4>
                                <dl class="grid grid-cols-1 gap-y-2 sm:grid-cols-2 sm:gap-x-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rumah Sakit</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->tenant->name ?? 'Tidak ada' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kode Rumah Sakit</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->tenant->code ?? 'Tidak ada' }}</dd>
                                    </div>
                                </dl>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">Informasi Kontak & Pekerjaan</h4>
                                <dl class="grid grid-cols-1 gap-y-2 sm:grid-cols-2 sm:gap-x-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">No. Telepon</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->phone ?? 'Tidak ada' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Posisi / Jabatan</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->position ?? 'Tidak ada' }}</dd>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Departemen</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->department ?? 'Tidak ada' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Aksi -->
            <div class="flex justify-end space-x-3">
                <form method="POST" action="{{ route('superadmin.users.toggle-active', $user->id) }}" class="inline-block">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 {{ $user->is_active ? 'bg-yellow-600 hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800' : 'bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-800' }} border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        @if($user->is_active)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            Nonaktifkan Pengguna
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Aktifkan Pengguna
                        @endif
                    </button>
                </form>
                
                @if(Auth::id() != $user->id)
                    <form method="POST" action="{{ route('superadmin.users.destroy', $user->id) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus Pengguna
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 