<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Faktor Penyebab Risiko') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('risk-management.factors.store') }}" class="max-w-xl mx-auto">
                        @csrf
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">{{ __('Informasi Faktor Penyebab') }}</h3>
                            
                            <!-- Laporan Risiko (report_id) -->
                            <div class="mb-4">
                                <x-input-label for="report_id" :value="__('Laporan Risiko')" />
                                <select id="report_id" name="report_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                                    <option value="">{{ __('-- Pilih Laporan Risiko --') }}</option>
                                    <option value="1">IKP-2023-001: Kesalahan identifikasi pasien</option>
                                    <option value="2">IKP-2023-002: Kerusakan alat monitor</option>
                                    <option value="3">IKP-2023-003: Pencahayaan tidak memadai</option>
                                </select>
                                <x-input-error :messages="$errors->get('report_id')" class="mt-2" />
                            </div>
                            
                            <!-- Jenis Faktor (factor_type) -->
                            <div class="mb-4">
                                <x-input-label for="factor_type" :value="__('Jenis Faktor')" />
                                <select id="factor_type" name="factor_type" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                                    <option value="">{{ __('-- Pilih Jenis Faktor --') }}</option>
                                    <option value="human">{{ __('Human (Manusia)') }}</option>
                                    <option value="equipment">{{ __('Equipment (Peralatan)') }}</option>
                                    <option value="environment">{{ __('Environment (Lingkungan)') }}</option>
                                    <option value="management">{{ __('Management (Manajemen)') }}</option>
                                    <option value="method">{{ __('Method (Metode)') }}</option>
                                    <option value="other">{{ __('Other (Lainnya)') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('factor_type')" class="mt-2" />
                            </div>
                            
                            <!-- Deskripsi (description) -->
                            <div class="mb-4">
                                <x-input-label for="description" :value="__('Deskripsi Faktor')" />
                                <textarea id="description" name="description" rows="4" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" placeholder="Jelaskan faktor penyebab risiko secara detail..." required></textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('risk-management.factors.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 active:bg-gray-500 dark:active:bg-gray-600 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 dark:ring-gray-700 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Simpan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 