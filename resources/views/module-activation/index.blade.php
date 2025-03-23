<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Permintaan Aktivasi Modul') }}
            </h2>
            @if(!auth()->user()->isSuperadmin())
                <a href="{{ route('module-activation.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Buat Permintaan
                </a>
            @endif
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
                    <!-- Filter Form -->
                    <form action="{{ route('module-activation.index') }}" method="GET" class="mb-6">
                        <div class="flex flex-col md:flex-row gap-4">
                            @if(auth()->user()->isSuperadmin() && isset($tenants) && $tenants->count() > 0)
                                <div class="w-full md:w-1/3">
                                    <label for="tenant_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rumah Sakit</label>
                                    <select id="tenant_id" name="tenant_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Semua Rumah Sakit</option>
                                        @foreach($tenants as $tenant)
                                            <option value="{{ $tenant->id }}" {{ $selectedTenant == $tenant->id ? 'selected' : '' }}>{{ $tenant->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            
                            <div class="w-full md:w-1/3">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="pending" {{ $selectedStatus == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="approved" {{ $selectedStatus == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="rejected" {{ $selectedStatus == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            
                            <div class="w-full md:w-1/3 flex items-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Request List -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Modul
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Rumah Sakit
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Pemohon
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tanggal Permintaan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($requests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $request->module->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $request->tenant->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $request->requestedBy->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $request->requested_at->format('d/m/Y H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($request->status == 'pending')
                                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                            @elseif($request->status == 'approved')
                                                bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                            @else
                                                bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                            @endif
                                            ">
                                                @if($request->status == 'pending')
                                                    Menunggu
                                                @elseif($request->status == 'approved')
                                                    Disetujui
                                                @else
                                                    Ditolak
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('module-activation.show', $request->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                Detail
                                            </a>
                                            
                                            @if(auth()->user()->isSuperadmin() && $request->status == 'pending')
                                                <span class="mx-1 text-gray-300 dark:text-gray-600">|</span>
                                                <button type="button" onclick="showProcessModal({{ $request->id }})" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                    Proses
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                            Tidak ada permintaan aktivasi modul
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Process Modal -->
    @if(auth()->user()->isSuperadmin())
        <div id="processModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden max-w-lg w-full mx-4">
                <form id="processForm" action="" method="POST">
                    @csrf
                    <div class="px-6 py-4 border-b dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Proses Permintaan Aktivasi</h3>
                    </div>
                    
                    <div class="px-6 py-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tindakan</label>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="action" value="approve" class="text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600" checked>
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Setujui</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="action" value="reject" class="text-red-600 focus:ring-red-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Tolak</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="process_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
                            <textarea id="process_notes" name="notes" rows="3" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-2">
                        <button type="button" id="cancelProcessBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Proses
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function showProcessModal(requestId) {
                const modal = document.getElementById('processModal');
                const form = document.getElementById('processForm');
                form.action = `/module-activation/${requestId}/process`;
                modal.classList.remove('hidden');
            }

            document.addEventListener('DOMContentLoaded', function() {
                const cancelBtn = document.getElementById('cancelProcessBtn');
                const modal = document.getElementById('processModal');
                
                cancelBtn.addEventListener('click', function() {
                    modal.classList.add('hidden');
                });
            });
        </script>
    @endif
</x-app-layout> 