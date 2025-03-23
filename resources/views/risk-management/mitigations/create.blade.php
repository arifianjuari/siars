<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Mitigasi Risiko') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('risk-management.mitigations.store') }}" class="max-w-xl mx-auto">
                        @csrf
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">{{ __('Informasi Mitigasi Risiko') }}</h3>
                            
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
                            
                            <!-- Deskripsi Tindakan (action_description) -->
                            <div class="mb-4">
                                <x-input-label for="action_description" :value="__('Deskripsi Tindakan Mitigasi')" />
                                <textarea id="action_description" name="action_description" rows="4" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" placeholder="Jelaskan tindakan mitigasi yang akan dilakukan secara detail..." required></textarea>
                                <x-input-error :messages="$errors->get('action_description')" class="mt-2" />
                            </div>
                            
                            <!-- Penanggung Jawab (responsible_id) -->
                            <div class="mb-4">
                                <x-input-label for="responsible_id" :value="__('Penanggung Jawab')" />
                                <select id="responsible_id" name="responsible_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                                    <option value="">{{ __('-- Pilih Penanggung Jawab --') }}</option>
                                    <option value="1">Dr. Rini Susanti (Kepala Unit)</option>
                                    <option value="2">Ir. Bambang Wijaya (Kepala Teknis)</option>
                                    <option value="3">Dadang Suryanto (Staff Pemeliharaan)</option>
                                </select>
                                <x-input-error :messages="$errors->get('responsible_id')" class="mt-2" />
                            </div>
                            
                            <!-- Deadline (target_date) -->
                            <div class="mb-4">
                                <x-input-label for="target_date" :value="__('Deadline')" />
                                <x-text-input id="target_date" type="date" name="target_date" class="block mt-1 w-full" required />
                                <x-input-error :messages="$errors->get('target_date')" class="mt-2" />
                            </div>
                            
                            <!-- Status Mitigasi (status) -->
                            <div class="mb-4">
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                                    <option value="">{{ __('-- Pilih Status --') }}</option>
                                    <option value="planned">{{ __('Planned (Direncanakan)') }}</option>
                                    <option value="in_progress">{{ __('In Progress (Sedang Berlangsung)') }}</option>
                                    <option value="completed">{{ __('Completed (Selesai)') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                            
                            <!-- Tanggal Penyelesaian (completion_date) -->
                            <div class="mb-4" id="completion_date_container" style="display: none;">
                                <x-input-label for="completion_date" :value="__('Tanggal Penyelesaian')" />
                                <x-text-input id="completion_date" type="date" name="completion_date" class="block mt-1 w-full" />
                                <x-input-error :messages="$errors->get('completion_date')" class="mt-2" />
                            </div>
                            
                            <!-- Efektivitas Mitigasi (effectiveness) -->
                            <div class="mb-4" id="effectiveness_container" style="display: none;">
                                <x-input-label for="effectiveness" :value="__('Efektivitas Mitigasi')" />
                                <select id="effectiveness" name="effectiveness" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="">{{ __('-- Pilih Efektivitas --') }}</option>
                                    <option value="effective">{{ __('Efektif') }}</option>
                                    <option value="partially">{{ __('Sebagian Efektif') }}</option>
                                    <option value="not_effective">{{ __('Tidak Efektif') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('effectiveness')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('risk-management.mitigations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 active:bg-gray-500 dark:active:bg-gray-600 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 dark:ring-gray-700 disabled:opacity-25 transition ease-in-out duration-150">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const completionDateContainer = document.getElementById('completion_date_container');
            const effectivenessContainer = document.getElementById('effectiveness_container');
            
            statusSelect.addEventListener('change', function() {
                if (this.value === 'completed') {
                    completionDateContainer.style.display = 'block';
                    effectivenessContainer.style.display = 'block';
                    document.getElementById('completion_date').setAttribute('required', 'required');
                    document.getElementById('effectiveness').setAttribute('required', 'required');
                } else {
                    completionDateContainer.style.display = 'none';
                    effectivenessContainer.style.display = 'none';
                    document.getElementById('completion_date').removeAttribute('required');
                    document.getElementById('effectiveness').removeAttribute('required');
                }
            });
        });
    </script>
</x-app-layout> 