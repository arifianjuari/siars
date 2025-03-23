<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Izin Modul') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">{{ __('Pengaturan Izin Modul Berdasarkan Role') }}</h3>
                        <div>
                            <button id="saveAllBtn" type="button" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                {{ __('Simpan Semua Perubahan') }}
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Role / Modul') }}
                                    </th>
                                    @foreach($modules as $module)
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <div class="flex flex-col items-center">
                                                <span class="mb-2">{{ $module->name }}</span>
                                                <span class="text-xs text-gray-400 normal-case">[{{ $module->code }}]</span>
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @foreach($roles as $role)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $role->name }}</div>
                                        </td>
                                        @foreach($modules as $module)
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @php
                                                    $hasPermission = isset($permissionsByRole[$role->id][$module->id]);
                                                    $canView = $hasPermission ? $permissionsByRole[$role->id][$module->id]['can_view'] : false;
                                                @endphp
                                                
                                                <div class="flex flex-col items-center">
                                                    <div class="mb-2">
                                                        @if($canView)
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                                                <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                Aktif
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                                <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                Tidak Aktif
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="flex space-x-2">
                                                        <a href="{{ route('superadmin.module-permissions.edit', ['roleId' => $role->id, 'moduleId' => $module->id]) }}" 
                                                           class="inline-flex items-center px-2 py-1 border border-gray-300 text-xs leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:text-gray-500 dark:hover:text-gray-200 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                                                            Edit
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Cepat -->
    <div id="quickEditModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-md w-full">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" id="modalTitle">Edit Izin</h3>
            </div>
            <div class="p-4">
                <form id="quickEditForm">
                    <input type="hidden" id="editRoleId" name="role_id">
                    <input type="hidden" id="editModuleId" name="module_id">
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="can_view" name="can_view" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="can_view" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">{{ __('Dapat Melihat') }}</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="can_create" name="can_create" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="can_create" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">{{ __('Dapat Membuat') }}</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="can_edit" name="can_edit" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="can_edit" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">{{ __('Dapat Mengedit') }}</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="can_delete" name="can_delete" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="can_delete" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">{{ __('Dapat Menghapus') }}</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="can_approve" name="can_approve" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="can_approve" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">{{ __('Dapat Menyetujui') }}</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="can_activate" name="can_activate" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="can_activate" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">{{ __('Dapat Mengaktifkan') }}</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button id="cancelEdit" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Batal') }}
                </button>
                <button id="saveEdit" type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Simpan') }}
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const quickEditModal = document.getElementById('quickEditModal');
            const quickEditForm = document.getElementById('quickEditForm');
            const cancelEditBtn = document.getElementById('cancelEdit');
            const saveEditBtn = document.getElementById('saveEdit');
            const modalTitle = document.getElementById('modalTitle');
            const saveAllBtn = document.getElementById('saveAllBtn');
            
            // Data untuk simpan semua perubahan
            let editedPermissions = [];
            
            // Fungsi untuk menampilkan modal edit cepat
            function showQuickEditModal(roleId, moduleId, roleName, moduleName, permissions) {
                document.getElementById('editRoleId').value = roleId;
                document.getElementById('editModuleId').value = moduleId;
                modalTitle.textContent = `Edit Izin: ${roleName} - ${moduleName}`;
                
                // Set nilai checkbox sesuai izin yang ada
                document.getElementById('can_view').checked = permissions.can_view || false;
                document.getElementById('can_create').checked = permissions.can_create || false;
                document.getElementById('can_edit').checked = permissions.can_edit || false;
                document.getElementById('can_delete').checked = permissions.can_delete || false;
                document.getElementById('can_approve').checked = permissions.can_approve || false;
                document.getElementById('can_activate').checked = permissions.can_activate || false;
                
                quickEditModal.classList.remove('hidden');
            }
            
            // Fungsi untuk menyembunyikan modal
            function hideQuickEditModal() {
                quickEditModal.classList.add('hidden');
            }
            
            // Event untuk tombol batal
            cancelEditBtn.addEventListener('click', hideQuickEditModal);
            
            // Event untuk tombol simpan
            saveEditBtn.addEventListener('click', function() {
                const roleId = document.getElementById('editRoleId').value;
                const moduleId = document.getElementById('editModuleId').value;
                
                const data = {
                    role_id: roleId,
                    module_id: moduleId,
                    can_view: document.getElementById('can_view').checked,
                    can_create: document.getElementById('can_create').checked,
                    can_edit: document.getElementById('can_edit').checked,
                    can_delete: document.getElementById('can_delete').checked,
                    can_approve: document.getElementById('can_approve').checked,
                    can_activate: document.getElementById('can_activate').checked,
                };
                
                // Tambahkan ke list perubahan
                const existingIndex = editedPermissions.findIndex(p => p.role_id == roleId && p.module_id == moduleId);
                if (existingIndex !== -1) {
                    editedPermissions[existingIndex] = data;
                } else {
                    editedPermissions.push(data);
                }
                
                // Panggil API untuk simpan perubahan
                fetch('{{ route("superadmin.module-permissions.bulk-update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        permissions: [data]
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        // Refresh halaman setelah berhasil menyimpan
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan perubahan');
                });
                
                hideQuickEditModal();
            });
            
            // Tampilkan modal saat tombol edit diklik
            document.querySelectorAll('.edit-permission-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const roleId = this.dataset.roleId;
                    const moduleId = this.dataset.moduleId;
                    const roleName = this.dataset.roleName;
                    const moduleName = this.dataset.moduleName;
                    
                    // Ambil data izin yang sudah ada
                    fetch(`{{ url('superadmin/module-permissions') }}/${roleId}/${moduleId}/edit`)
                        .then(response => response.json())
                        .then(data => {
                            showQuickEditModal(roleId, moduleId, roleName, moduleName, data.permission);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat mengambil data izin');
                        });
                });
            });
            
            // Event untuk tombol simpan semua perubahan
            saveAllBtn.addEventListener('click', function() {
                if (editedPermissions.length === 0) {
                    alert('Tidak ada perubahan untuk disimpan');
                    return;
                }
                
                fetch('{{ route("superadmin.module-permissions.bulk-update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        permissions: editedPermissions
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert(data.message);
                        // Reset list perubahan
                        editedPermissions = [];
                        // Refresh halaman
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan perubahan');
                });
            });
        });
    </script>
    @endpush
</x-app-layout> 