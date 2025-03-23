<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Risiko') }} - 
            @if($type == 'matrix')
                {{ __('Matriks Risiko') }}
            @elseif($type == 'category')
                {{ __('Risiko Per Kategori') }}
            @elseif($type == 'trend')
                {{ __('Tren Risiko') }}
            @else
                {{ __('Detail Laporan') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">
                            @if($type == 'matrix')
                                {{ __('Matriks Risiko') }}
                            @elseif($type == 'category')
                                {{ __('Risiko Per Kategori') }}
                            @elseif($type == 'trend')
                                {{ __('Tren Risiko') }}
                            @else
                                {{ __('Detail Laporan') }}
                            @endif
                        </h3>
                        <div class="flex">
                            <form action="{{ route('risk-management.reports.pdf', $type) }}" method="POST" class="mr-2">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:bg-red-700 dark:focus:bg-red-600 active:bg-red-800 dark:active:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Unduh PDF') }}
                                </button>
                            </form>
                            <form action="{{ route('risk-management.reports.excel', $type) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 focus:bg-green-700 dark:focus:bg-green-600 active:bg-green-800 dark:active:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Unduh Excel') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <div class="mb-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center mb-4">
                                <span class="text-gray-700 dark:text-gray-300 font-medium mr-2">Filter:</span>
                                <select class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm mr-4">
                                    <option value="">Semua Kategori</option>
                                    <option value="1">Risiko Klinis</option>
                                    <option value="2">Risiko Non-Klinis</option>
                                </select>
                                <span class="text-gray-700 dark:text-gray-300 font-medium mr-2">Periode:</span>
                                <select class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                    <option value="">Semua Waktu</option>
                                    <option value="1">Bulan Ini</option>
                                    <option value="2">3 Bulan Terakhir</option>
                                    <option value="3">6 Bulan Terakhir</option>
                                    <option value="4">1 Tahun Terakhir</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    @if($type == 'matrix')
                        <!-- Matriks Risiko -->
                        <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm rounded-lg">
                            <div class="p-6">
                                <h4 class="text-lg font-semibold mb-4">{{ __('Matriks Risiko (Dampak x Probabilitas)') }}</h4>
                                
                                <div class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-2 border"></th>
                                                <th class="px-4 py-2 border text-center" colspan="5">Dampak</th>
                                            </tr>
                                            <tr>
                                                <th class="px-4 py-2 border"></th>
                                                <th class="px-4 py-2 border text-center">1</th>
                                                <th class="px-4 py-2 border text-center">2</th>
                                                <th class="px-4 py-2 border text-center">3</th>
                                                <th class="px-4 py-2 border text-center">4</th>
                                                <th class="px-4 py-2 border text-center">5</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th class="px-4 py-2 border text-center">5</th>
                                                <td class="px-4 py-10 border bg-yellow-100 dark:bg-yellow-800 text-center">5</td>
                                                <td class="px-4 py-10 border bg-yellow-200 dark:bg-yellow-700 text-center">10</td>
                                                <td class="px-4 py-10 border bg-orange-200 dark:bg-orange-700 text-center">15</td>
                                                <td class="px-4 py-10 border bg-red-200 dark:bg-red-700 text-center">20</td>
                                                <td class="px-4 py-10 border bg-red-300 dark:bg-red-600 text-center">25</td>
                                            </tr>
                                            <tr>
                                                <th class="px-4 py-2 border text-center">4</th>
                                                <td class="px-4 py-10 border bg-green-100 dark:bg-green-800 text-center">4</td>
                                                <td class="px-4 py-10 border bg-yellow-100 dark:bg-yellow-800 text-center">8</td>
                                                <td class="px-4 py-10 border bg-yellow-200 dark:bg-yellow-700 text-center">12</td>
                                                <td class="px-4 py-10 border bg-red-200 dark:bg-red-700 text-center">16
                                                    <div class="mt-1 text-xs">Risiko ID-1</div>
                                                </td>
                                                <td class="px-4 py-10 border bg-red-300 dark:bg-red-600 text-center">20</td>
                                            </tr>
                                            <tr>
                                                <th class="px-4 py-2 border text-center">3</th>
                                                <td class="px-4 py-10 border bg-green-100 dark:bg-green-800 text-center">3</td>
                                                <td class="px-4 py-10 border bg-green-200 dark:bg-green-700 text-center">6</td>
                                                <td class="px-4 py-10 border bg-yellow-100 dark:bg-yellow-800 text-center">9
                                                    <div class="mt-1 text-xs">Risiko ID-2</div>
                                                </td>
                                                <td class="px-4 py-10 border bg-yellow-200 dark:bg-yellow-700 text-center">12</td>
                                                <td class="px-4 py-10 border bg-orange-200 dark:bg-orange-700 text-center">15</td>
                                            </tr>
                                            <tr>
                                                <th class="px-4 py-2 border text-center">2</th>
                                                <td class="px-4 py-10 border bg-green-100 dark:bg-green-800 text-center">2</td>
                                                <td class="px-4 py-10 border bg-green-100 dark:bg-green-800 text-center">4</td>
                                                <td class="px-4 py-10 border bg-green-200 dark:bg-green-700 text-center">6</td>
                                                <td class="px-4 py-10 border bg-yellow-100 dark:bg-yellow-800 text-center">8</td>
                                                <td class="px-4 py-10 border bg-yellow-200 dark:bg-yellow-700 text-center">10</td>
                                            </tr>
                                            <tr>
                                                <th class="px-4 py-2 border text-center">1</th>
                                                <td class="px-4 py-10 border bg-green-100 dark:bg-green-800 text-center">1</td>
                                                <td class="px-4 py-10 border bg-green-100 dark:bg-green-800 text-center">2</td>
                                                <td class="px-4 py-10 border bg-green-100 dark:bg-green-800 text-center">3</td>
                                                <td class="px-4 py-10 border bg-green-200 dark:bg-green-700 text-center">4</td>
                                                <td class="px-4 py-10 border bg-green-200 dark:bg-green-700 text-center">5</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 bg-green-100 dark:bg-green-800 mr-2"></div>
                                        <span>Risiko Rendah (1-4)</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 bg-yellow-100 dark:bg-yellow-800 mr-2"></div>
                                        <span>Risiko Sedang (5-9)</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 bg-red-200 dark:bg-red-700 mr-2"></div>
                                        <span>Risiko Tinggi (10-25)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($type == 'category')
                        <!-- Risiko Per Kategori -->
                        <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm rounded-lg">
                            <div class="p-6">
                                <h4 class="text-lg font-semibold mb-4">{{ __('Jumlah Risiko Per Kategori') }}</h4>
                                
                                <div class="relative mb-8" style="height: 300px;">
                                    <!-- Chart placeholders -->
                                    <div class="absolute bottom-0 left-0 w-1/3 h-1/3 bg-green-500 dark:bg-green-600"></div>
                                    <div class="absolute bottom-0 left-1/3 w-1/3 h-1/2 bg-yellow-500 dark:bg-yellow-600"></div>
                                    <div class="absolute bottom-0 left-2/3 w-1/3 h-2/3 bg-red-500 dark:bg-red-600"></div>
                                    
                                    <!-- X axis labels -->
                                    <div class="absolute bottom-[-30px] left-[calc(1/6*100%)] transform translate-x-[-50%] text-center">
                                        <div>Risiko Rendah</div>
                                    </div>
                                    <div class="absolute bottom-[-30px] left-[calc(3/6*100%)] transform translate-x-[-50%] text-center">
                                        <div>Risiko Sedang</div>
                                    </div>
                                    <div class="absolute bottom-[-30px] left-[calc(5/6*100%)] transform translate-x-[-50%] text-center">
                                        <div>Risiko Tinggi</div>
                                    </div>
                                </div>

                                <div class="overflow-x-auto mt-8">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                        <thead class="bg-gray-50 dark:bg-gray-800">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Kategori') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Risiko Rendah') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Risiko Sedang') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Risiko Tinggi') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">Risiko Klinis</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">2</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">3</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">1</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">6</td>
                                            </tr>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">Risiko Non-Klinis</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">1</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">2</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">0</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">3</td>
                                            </tr>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">Total</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">3</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">5</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">1</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">9</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @elseif($type == 'trend')
                        <!-- Tren Risiko -->
                        <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm rounded-lg">
                            <div class="p-6">
                                <h4 class="text-lg font-semibold mb-4">{{ __('Tren Risiko') }}</h4>
                                
                                <div class="relative mb-8" style="height: 300px;">
                                    <!-- Chart placeholders - simplified trend line -->
                                    <div class="absolute bottom-0 left-0 w-full h-full flex items-end">
                                        <div class="w-1/6 h-1/3 border-b-2 border-r-2 border-blue-500"></div>
                                        <div class="w-1/6 h-2/5 border-b-2 border-r-2 border-blue-500"></div>
                                        <div class="w-1/6 h-1/2 border-b-2 border-r-2 border-blue-500"></div>
                                        <div class="w-1/6 h-3/5 border-b-2 border-r-2 border-blue-500"></div>
                                        <div class="w-1/6 h-1/2 border-b-2 border-r-2 border-blue-500"></div>
                                        <div class="w-1/6 h-2/5 border-b-2 border-blue-500"></div>
                                    </div>
                                    
                                    <!-- X axis labels -->
                                    <div class="absolute bottom-[-30px] left-[calc(1/12*100%)] transform translate-x-[-50%] text-center">
                                        <div>Jan</div>
                                    </div>
                                    <div class="absolute bottom-[-30px] left-[calc(3/12*100%)] transform translate-x-[-50%] text-center">
                                        <div>Feb</div>
                                    </div>
                                    <div class="absolute bottom-[-30px] left-[calc(5/12*100%)] transform translate-x-[-50%] text-center">
                                        <div>Mar</div>
                                    </div>
                                    <div class="absolute bottom-[-30px] left-[calc(7/12*100%)] transform translate-x-[-50%] text-center">
                                        <div>Apr</div>
                                    </div>
                                    <div class="absolute bottom-[-30px] left-[calc(9/12*100%)] transform translate-x-[-50%] text-center">
                                        <div>Mei</div>
                                    </div>
                                    <div class="absolute bottom-[-30px] left-[calc(11/12*100%)] transform translate-x-[-50%] text-center">
                                        <div>Jun</div>
                                    </div>
                                </div>

                                <div class="overflow-x-auto mt-8">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                        <thead class="bg-gray-50 dark:bg-gray-800">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Bulan') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Risiko Rendah') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Risiko Sedang') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Risiko Tinggi') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">Januari</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">3</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">2</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">0</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">5</td>
                                            </tr>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">Februari</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">2</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">3</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">1</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">6</td>
                                            </tr>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">Maret</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">2</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">4</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">1</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">7</td>
                                            </tr>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">April</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">1</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">5</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">2</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">8</td>
                                            </tr>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">Mei</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">2</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">4</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">1</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">7</td>
                                            </tr>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">Juni</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">3</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">3</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">0</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">6</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end mt-6">
                        <a href="{{ route('risk-management.reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-800 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Kembali') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 