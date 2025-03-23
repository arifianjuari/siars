<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Penilaian Risiko') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">{{ __('Informasi Penilaian Risiko') }}</h3>
                        <div>
                            <a href="{{ route('risk-management.reviews.edit', $id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:bg-indigo-700 dark:focus:bg-indigo-600 active:bg-indigo-800 dark:active:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-2">
                                {{ __('Edit') }}
                            </a>
                            <a href="{{ route('risk-management.reviews.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-800 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Kembali') }}
                            </a>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                        <div class="border-b dark:border-gray-600 pb-3 mb-3">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Kategori Risiko') }}</h4>
                            <p class="mt-1 text-base text-gray-900 dark:text-gray-100">
                                @if($id == 1)
                                    Risiko Klinis
                                @elseif($id == 2)
                                    Risiko Non-Klinis
                                @else
                                    {{ __('Kategori Tidak Diketahui') }}
                                @endif
                            </p>
                        </div>

                        <div class="border-b dark:border-gray-600 pb-3 mb-3">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Deskripsi Risiko') }}</h4>
                            <p class="mt-1 text-base text-gray-900 dark:text-gray-100">
                                @if($id == 1)
                                    Risiko kesalahan identifikasi pasien saat proses pendaftaran dan pemberian obat.
                                @elseif($id == 2)
                                    Risiko keamanan data pasien pada sistem informasi rumah sakit.
                                @else
                                    {{ __('Tidak ada data') }}
                                @endif
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b dark:border-gray-600 pb-3 mb-3">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tanggal Penilaian') }}</h4>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">
                                    @if($id == 1)
                                        15 Juli 2023
                                    @elseif($id == 2)
                                        22 Agustus 2023
                                    @else
                                        {{ __('-') }}
                                    @endif
                                </p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Status') }}</h4>
                                <p class="mt-1">
                                    @if($id == 1)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">{{ __('Aktif') }}</span>
                                    @elseif($id == 2)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">{{ __('Aktif') }}</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100">{{ __('Tidak Diketahui') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b dark:border-gray-600 pb-3 mb-3">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Dampak') }}</h4>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">
                                    @if($id == 1)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">4 - Tinggi</span>
                                    @elseif($id == 2)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">3 - Sedang</span>
                                    @else
                                        {{ __('-') }}
                                    @endif
                                </p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Probabilitas') }}</h4>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">
                                    @if($id == 1)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">4 - Sering</span>
                                    @elseif($id == 2)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">3 - Kadang-kadang</span>
                                    @else
                                        {{ __('-') }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="border-b dark:border-gray-600 pb-3 mb-3">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nilai Risiko') }}</h4>
                            <p class="mt-1">
                                @if($id == 1)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">Tinggi (16)</span>
                                @elseif($id == 2)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">Sedang (9)</span>
                                @else
                                    {{ __('-') }}
                                @endif
                            </p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Mitigasi Risiko') }}</h4>
                            <div class="mt-1 text-base text-gray-900 dark:text-gray-100">
                                @if($id == 1)
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Penerapan gelang identitas pasien dengan minimal dua identifikasi (nama dan tanggal lahir)</li>
                                        <li>Pelatihan staf tentang prosedur identifikasi pasien yang benar</li>
                                        <li>Audit berkala terhadap kepatuhan identifikasi pasien</li>
                                        <li>Implementasi sistem barcode untuk pemberian obat</li>
                                    </ul>
                                @elseif($id == 2)
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Penerapan sistem keamanan berlapis pada akses data pasien</li>
                                        <li>Pembatasan akses data sesuai dengan peran dan tanggung jawab</li>
                                        <li>Audit log akses data secara rutin</li>
                                        <li>Backup data secara berkala dan pengujian pemulihan</li>
                                    </ul>
                                @else
                                    {{ __('Tidak ada data') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 