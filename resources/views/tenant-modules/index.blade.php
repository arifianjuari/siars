<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Modul Rumah Sakit') }} - {{ $tenant->name }}
            </h2>
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

            <div class="mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Daftar Modul</h3>
                            @if(auth()->user()->isSuperadmin())
                                <a href="{{ route('module-activation.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                    Lihat Permintaan Aktivasi
                                </a>
                            @else
                                <a href="{{ route('module-activation.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Ajukan Aktivasi Modul
                                </a>
                            @endif
                        </div>

                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Nama Modul
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Tipe
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
                                    @forelse($modules as $module)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $module->name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ Str::limit($module->description, 50) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($module->is_core)
                                                    bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                                @else
                                                    bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300
                                                @endif
                                                ">
                                                    {{ $module->is_core ? 'Inti' : 'Opsional' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $isActive = $module->pivot ? $module->pivot->is_active : false;
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($isActive)
                                                    bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                @else
                                                    bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                @endif
                                                ">
                                                    {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('modules.show', $module->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3">
                                                    Detail
                                                </a>

                                                @if(auth()->user()->isSuperadmin())
                                                    @php
                                                        $isActive = $module->pivot ? $module->pivot->is_active : false;
                                                    @endphp

                                                    @if($isActive)
                                                        <form action="{{ route('tenant.modules.deactivate', $module->id) }}" method="POST" class="inline-block">
                                                            @csrf
                                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                                                Nonaktifkan
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('tenant.modules.activate', $module->id) }}" method="POST" class="inline-block">
                                                            @csrf
                                                            <button type="submit" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                                                Aktifkan
                                                            </button>
                                                        </form>
                                                    @endif
                                                @elseif(!$isActive)
                                                    <form action="{{ route('tenant.modules.request-activation') }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <input type="hidden" name="module_id" value="{{ $module->id }}">
                                                        <button type="submit" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                            Ajukan Aktivasi
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                                Tidak ada modul yang tersedia untuk rumah sakit ini
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 