<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Penilaian Risiko') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('risk-management.reviews.update', $id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Kategori Risiko') }}</label>
                            <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="">{{ __('Pilih Kategori Risiko') }}</option>
                                <option value="1" {{ $id == 1 ? 'selected' : '' }}>Risiko Klinis</option>
                                <option value="2" {{ $id == 2 ? 'selected' : '' }}>Risiko Non-Klinis</option>
                            </select>
                            @error('category_id')
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Deskripsi Risiko') }}</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">{{ $id == 1 ? 'Risiko kesalahan identifikasi pasien saat proses pendaftaran dan pemberian obat.' : ($id == 2 ? 'Risiko keamanan data pasien pada sistem informasi rumah sakit.' : '') }}</textarea>
                            @error('description')
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="impact" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Dampak (1-5)') }}</label>
                                <select id="impact" name="impact" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                    <option value="">{{ __('Pilih Dampak') }}</option>
                                    <option value="1" {{ $id == 1 && 4 == 1 ? 'selected' : ($id == 2 && 3 == 1 ? 'selected' : '') }}>1 - Sangat Rendah</option>
                                    <option value="2" {{ $id == 1 && 4 == 2 ? 'selected' : ($id == 2 && 3 == 2 ? 'selected' : '') }}>2 - Rendah</option>
                                    <option value="3" {{ $id == 1 && 4 == 3 ? 'selected' : ($id == 2 && 3 == 3 ? 'selected' : '') }}>3 - Sedang</option>
                                    <option value="4" {{ $id == 1 && 4 == 4 ? 'selected' : ($id == 2 && 3 == 4 ? 'selected' : '') }}>4 - Tinggi</option>
                                    <option value="5" {{ $id == 1 && 4 == 5 ? 'selected' : ($id == 2 && 3 == 5 ? 'selected' : '') }}>5 - Sangat Tinggi</option>
                                </select>
                                @error('impact')
                                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="probability" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Probabilitas (1-5)') }}</label>
                                <select id="probability" name="probability" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                    <option value="">{{ __('Pilih Probabilitas') }}</option>
                                    <option value="1" {{ $id == 1 && 4 == 1 ? 'selected' : ($id == 2 && 3 == 1 ? 'selected' : '') }}>1 - Sangat Jarang</option>
                                    <option value="2" {{ $id == 1 && 4 == 2 ? 'selected' : ($id == 2 && 3 == 2 ? 'selected' : '') }}>2 - Jarang</option>
                                    <option value="3" {{ $id == 1 && 4 == 3 ? 'selected' : ($id == 2 && 3 == 3 ? 'selected' : '') }}>3 - Kadang-kadang</option>
                                    <option value="4" {{ $id == 1 && 4 == 4 ? 'selected' : ($id == 2 && 3 == 4 ? 'selected' : '') }}>4 - Sering</option>
                                    <option value="5" {{ $id == 1 && 4 == 5 ? 'selected' : ($id == 2 && 3 == 5 ? 'selected' : '') }}>5 - Sangat Sering</option>
                                </select>
                                @error('probability')
                                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="mitigation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Mitigasi Risiko') }}</label>
                            <textarea name="mitigation" id="mitigation" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">{{ $id == 1 ? "Penerapan gelang identitas pasien dengan minimal dua identifikasi (nama dan tanggal lahir)\nPelatihan staf tentang prosedur identifikasi pasien yang benar\nAudit berkala terhadap kepatuhan identifikasi pasien\nImplementasi sistem barcode untuk pemberian obat" : ($id == 2 ? "Penerapan sistem keamanan berlapis pada akses data pasien\nPembatasan akses data sesuai dengan peran dan tanggung jawab\nAudit log akses data secara rutin\nBackup data secara berkala dan pengujian pemulihan" : '') }}</textarea>
                            @error('mitigation')
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('risk-management.reviews.show', $id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-800 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-3">
                                {{ __('Batal') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:bg-indigo-700 dark:focus:bg-indigo-600 active:bg-indigo-800 dark:active:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Simpan Perubahan') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 