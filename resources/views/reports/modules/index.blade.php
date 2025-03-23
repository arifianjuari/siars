<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Modul') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Filter Laporan</h3>
                    </div>
                    
                    <form action="{{ route('reports.modules.index') }}" method="GET" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                            <x-text-input id="start_date" type="date" name="start_date" class="mt-1 block w-full" :value="$startDate" />
                        </div>
                        
                        <div class="flex-1 min-w-[200px]">
                            <x-input-label for="end_date" :value="__('Tanggal Akhir')" />
                            <x-text-input id="end_date" type="date" name="end_date" class="mt-1 block w-full" :value="$endDate" />
                        </div>
                        
                        <div class="flex items-end">
                            <x-primary-button class="ml-4">
                                {{ __('Filter') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Statistik Aktivasi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium mb-4">Statistik Aktivasi Modul</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Total Aktivasi</p>
                            <p class="text-2xl font-bold">{{ $stats['total_activated'] }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Total Permintaan</p>
                            <p class="text-2xl font-bold">{{ $stats['total_requested'] }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Tingkat Persetujuan</p>
                            <p class="text-2xl font-bold">{{ $stats['acceptance_rate'] }}%</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modul Aktif -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Modul Aktif</h3>
                        <a href="{{ route('reports.modules.export.active') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            Ekspor CSV
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modul</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Aktivasi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diaktifkan Oleh</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($activeModules as $module)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $module->tenant_name }} ({{ $module->tenant_code }})</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $module->module_name }} ({{ $module->module_code }})</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $module->module_description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $module->activated_at }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $module->approved_by_name ?? '-' }}</td>
                                </tr>
                                @endforeach
                                
                                @if(count($activeModules) === 0)
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada modul aktif</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 text-right">
                        <a href="{{ route('reports.modules.active') }}" class="text-indigo-600 hover:text-indigo-900">Lihat Semua</a>
                    </div>
                </div>
            </div>
            
            <!-- Histori Aktivasi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Histori Aktivasi Modul</h3>
                        <a href="{{ route('reports.modules.export.history', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            Ekspor CSV
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modul</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($activationHistory as $history)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $history['action_at'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $history['action_text'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $history['module_name'] }} ({{ $history['module_code'] }})</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $history['tenant_name'] }} ({{ $history['tenant_code'] }})</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $history['user_name'] }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $history['notes'] }}</td>
                                </tr>
                                @endforeach
                                
                                @if(count($activationHistory) === 0)
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada histori aktivasi</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 text-right">
                        <a href="{{ route('reports.modules.history', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-indigo-600 hover:text-indigo-900">Lihat Semua</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 