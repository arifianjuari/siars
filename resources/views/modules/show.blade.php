<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail Modul') }}
            </h2>
            <a href="{{ route('modules.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="sm:flex sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $module->name }}
                            </h3>
                            <div class="mt-2 flex items-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($module->is_core)
                                    bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                @else
                                    bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300
                                @endif
                                ">
                                    {{ $module->is_core ? 'Modul Inti' : 'Modul Opsional' }}
                                </span>
                                
                                @if($tenantHasModule)
                                    <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($isModuleActive)
                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @else
                                        bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @endif
                                    ">
                                        {{ $isModuleActive ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Action buttons -->
                        <div class="mt-4 sm:mt-0">
                            @if(auth()->user()->isSuperadmin() && $tenantHasModule)
                                @if($isModuleActive)
                                    <form action="{{ route('tenant.modules.deactivate', $module->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            Nonaktifkan
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('tenant.modules.activate', $module->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Aktifkan
                                        </button>
                                    </form>
                                @endif
                            @elseif($tenantHasModule && !$isModuleActive && !auth()->user()->isSuperadmin())
                                <button type="button" id="requestActivationBtn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Ajukan Aktivasi
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Deskripsi</h4>
                        <p class="text-gray-700 dark:text-gray-300">
                            {{ $module->description ?? 'Tidak ada deskripsi' }}
                        </p>
                    </div>
                    
                    @if($module->features)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Fitur</h4>
                            <ul class="list-disc pl-5 space-y-1 text-gray-700 dark:text-gray-300">
                                @foreach(explode("\n", $module->features) as $feature)
                                    @if(trim($feature))
                                        <li>{{ trim($feature) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    @if($tenantHasModule && !$isModuleActive && !auth()->user()->isSuperadmin())
                        <!-- Modal untuk permintaan aktivasi -->
                        <div id="requestActivationModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden max-w-lg w-full mx-4">
                                <form action="{{ route('tenant.modules.request-activation') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="module_id" value="{{ $module->id }}">
                                    
                                    <div class="px-6 py-4 border-b dark:border-gray-700">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Ajukan Aktivasi Modul</h3>
                                    </div>
                                    
                                    <div class="px-6 py-4">
                                        <div class="mb-4">
                                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan (opsional)</label>
                                            <textarea id="notes" name="notes" rows="3" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                                        </div>
                                        
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Permintaan aktivasi akan dikirim ke superadmin untuk disetujui.
                                        </p>
                                    </div>
                                    
                                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-2">
                                        <button type="button" id="cancelRequestBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Batal
                                        </button>
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Kirim Permintaan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const requestBtn = document.getElementById('requestActivationBtn');
                                const modal = document.getElementById('requestActivationModal');
                                const cancelBtn = document.getElementById('cancelRequestBtn');
                                
                                requestBtn.addEventListener('click', function() {
                                    modal.classList.remove('hidden');
                                });
                                
                                cancelBtn.addEventListener('click', function() {
                                    modal.classList.add('hidden');
                                });
                            });
                        </script>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 