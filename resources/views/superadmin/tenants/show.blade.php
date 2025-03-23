<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Rumah Sakit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4">
                        <a href="{{ route('superadmin.tenants.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                    
                    <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                        <!-- Informasi Utama -->
                        <div class="w-full md:w-2/3 bg-white dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $tenant->name }}</h3>
                                <span class="px-3 py-1 {{ $tenant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full text-xs font-semibold">
                                    {{ $tenant->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Kode Rumah Sakit</h4>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $tenant->code }}</p>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Dibuat</h4>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $tenant->created_at->format('d F Y') }}</p>
                                </div>

                                <div class="col-span-2">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi</h4>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $tenant->description ?? 'Tidak ada deskripsi' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logo & Kontak -->
                        <div class="w-full md:w-1/3 bg-white dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                            @if($tenant->logo_path)
                                <div class="mb-4 flex justify-center">
                                    <img src="{{ asset('storage/' . $tenant->logo_path) }}" alt="{{ $tenant->name }}" class="h-32 w-auto object-contain">
                                </div>
                            @endif

                            <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Informasi Kontak</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</h4>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $tenant->email ?? 'Tidak ada data' }}</p>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Telepon</h4>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $tenant->phone ?? 'Tidak ada data' }}</p>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Website</h4>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        @if($tenant->website)
                                            <a href="{{ $tenant->website }}" target="_blank" class="text-blue-500 hover:text-blue-700">{{ $tenant->website }}</a>
                                        @else
                                            Tidak ada data
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="mt-6 bg-white dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                        <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Alamat Lengkap</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-y-4 gap-x-6">
                            <div class="md:col-span-2">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Alamat</h4>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $tenant->address ?? 'Tidak ada data' }}</p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Kota</h4>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $tenant->city ?? 'Tidak ada data' }}</p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Provinsi</h4>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $tenant->province ?? 'Tidak ada data' }}</p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Kode Pos</h4>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $tenant->postal_code ?? 'Tidak ada data' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex justify-end mt-6 space-x-3">
                        <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit
                        </a>
                        
                        <form action="{{ route('superadmin.set-tenant', ['tenant_id' => $tenant->id]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Pilih Rumah Sakit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 