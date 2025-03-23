@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Pengaturan Jenis Insiden</h1>
        </div>
    </div>

    <style>
        .toggle-subtypes {
            cursor: pointer;
            transition: all 0.2s;
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        .toggle-subtypes:hover {
            background-color: #e9ecef;
        }
        .subtype-container {
            transition: all 0.3s ease;
        }
        .type-row {
            border-left: 3px solid transparent;
        }
        .type-row-active {
            border-left: 3px solid #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }
    </style>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tambah Jenis Insiden Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('risk-management.settings.incident-types.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Jenis Insiden</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label">Kode</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}">
                            @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Jenis Insiden</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">Daftar Jenis Insiden</h5>
                        </div>
                        <div class="col-auto">
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Cari...">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%"></th>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($incidentTypes as $type)
                                <tr class="type-row" data-type-id="{{ $type->id }}">
                                    <td class="text-center">
                                        @if($type->subtypes->count() > 0)
                                            <button class="btn btn-sm btn-light toggle-subtypes" data-type-id="{{ $type->id }}">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        @endif
                                    </td>
                                    <td>{{ $type->code }}</td>
                                    <td>{{ $type->name }}</td>
                                    <td>{{ $type->description }}</td>
                                    <td>
                                        @if($type->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                        @else
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editTypeModal{{ $type->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#subtypesModal{{ $type->id }}">
                                                <i class="fas fa-list"></i> Subtipe
                                            </button>
                                            @if($type->incidents()->count() == 0)
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTypeModal{{ $type->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                <!-- Subtypes Row (hidden by default) -->
                                @if($type->subtypes->count() > 0)
                                <tr class="subtype-container bg-light" data-parent="{{ $type->id }}" style="display: none;">
                                    <td colspan="6" class="p-0">
                                        <div class="p-3">
                                            <h6 class="mb-2">Subtipe dari {{ $type->name }}</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead class="table-secondary">
                                                        <tr>
                                                            <th>Nama</th>
                                                            <th>Deskripsi</th>
                                                            <th width="10%">Status</th>
                                                            <th width="15%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($type->subtypes as $subtype)
                                                        <tr>
                                                            <td>{{ $subtype->name }}</td>
                                                            <td>{{ $subtype->description }}</td>
                                                            <td>
                                                                @if($subtype->is_active)
                                                                <span class="badge bg-success">Aktif</span>
                                                                @else
                                                                <span class="badge bg-danger">Tidak Aktif</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm" role="group">
                                                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editSubtypeModal{{ $subtype->id }}">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSubtypeModal{{ $subtype->id }}">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#subtypesModal{{ $type->id }}">
                                                    <i class="fas fa-plus"></i> Tambah Subtipe
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada jenis insiden yang tersedia</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals for Edit and Delete -->
    @foreach($incidentTypes as $type)
    <!-- Edit Type Modal -->
    <div class="modal fade" id="editTypeModal{{ $type->id }}" tabindex="-1" aria-labelledby="editTypeModalLabel{{ $type->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTypeModalLabel{{ $type->id }}">Edit Jenis Insiden</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('risk-management.settings.incident-types.update', $type->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name{{ $type->id }}" class="form-label">Nama Jenis Insiden</label>
                            <input type="text" class="form-control" id="edit_name{{ $type->id }}" name="name" value="{{ $type->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_code{{ $type->id }}" class="form-label">Kode</label>
                            <input type="text" class="form-control" id="edit_code{{ $type->id }}" name="code" value="{{ $type->code }}">
                        </div>
                        <div class="mb-3">
                            <label for="edit_description{{ $type->id }}" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_description{{ $type->id }}" name="description" rows="3">{{ $type->description }}</textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_is_active{{ $type->id }}" name="is_active" {{ $type->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="edit_is_active{{ $type->id }}">
                                Aktif
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Type Modal -->
    <div class="modal fade" id="deleteTypeModal{{ $type->id }}" tabindex="-1" aria-labelledby="deleteTypeModalLabel{{ $type->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTypeModalLabel{{ $type->id }}">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus jenis insiden <strong>{{ $type->name }}</strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('risk-management.settings.incident-types.delete', $type->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk subtipe -->
    <div class="modal fade" id="subtypesModal{{ $type->id }}" tabindex="-1" aria-labelledby="subtypesModalLabel{{ $type->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subtypesModalLabel{{ $type->id }}">Kelola Subtipe untuk {{ $type->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form tambah subtipe -->
                    <form action="{{ route('risk-management.settings.incident-subtypes.store') }}" method="POST" class="mb-4">
                        @csrf
                        <input type="hidden" name="incident_type_id" value="{{ $type->id }}">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <input type="text" name="name" class="form-control" placeholder="Nama Subtipe" required>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="description" class="form-control" placeholder="Deskripsi (opsional)">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Tambah</button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Tabel subtipe -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($type->subtypes as $subtype)
                                <tr>
                                    <td>{{ $subtype->name }}</td>
                                    <td>{{ $subtype->description }}</td>
                                    <td>
                                        @if($subtype->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                        @else
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editSubtypeModal{{ $subtype->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSubtypeModal{{ $subtype->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada subtipe untuk jenis insiden ini</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @foreach($type->subtypes as $subtype)
    <!-- Modal Edit Subtipe -->
    <div class="modal fade" id="editSubtypeModal{{ $subtype->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Subtipe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('risk-management.settings.incident-subtypes.update', $subtype->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Subtipe</label>
                            <input type="text" name="name" class="form-control" value="{{ $subtype->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" name="description" class="form-control" value="{{ $subtype->description }}">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active_{{ $subtype->id }}" {{ $subtype->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active_{{ $subtype->id }}">Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Hapus Subtipe -->
    <div class="modal fade" id="deleteSubtypeModal{{ $subtype->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Subtipe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus subtipe <strong>{{ $subtype->name }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('risk-management.settings.incident-subtypes.delete', $subtype->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @endforeach
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('tbody tr');
        
        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            
            // Hide all subtype containers first
            document.querySelectorAll('.subtype-container').forEach(el => {
                el.style.display = 'none';
            });
            
            // Reset all toggle icons
            document.querySelectorAll('.toggle-subtypes i').forEach(icon => {
                icon.className = 'fas fa-chevron-down';
            });
            
            // Filter parent rows
            document.querySelectorAll('.type-row').forEach(row => {
                if (row.querySelector('td:first-child')) {
                    const rowText = row.textContent.toLowerCase();
                    const typeId = row.getAttribute('data-type-id');
                    const matchesSearch = searchTerm === '' || rowText.includes(searchTerm);
                    
                    row.style.display = matchesSearch ? '' : 'none';
                }
            });
        }
        
        // Toggle subtypes visibility
        document.querySelectorAll('.toggle-subtypes').forEach(button => {
            button.addEventListener('click', function() {
                const typeId = this.getAttribute('data-type-id');
                const icon = this.querySelector('i');
                const subtypeRow = document.querySelector(`.subtype-container[data-parent="${typeId}"]`);
                const parentRow = document.querySelector(`.type-row[data-type-id="${typeId}"]`);
                
                if (subtypeRow.style.display === 'none') {
                    subtypeRow.style.display = 'table-row';
                    icon.className = 'fas fa-chevron-up';
                    parentRow.classList.add('type-row-active');
                } else {
                    subtypeRow.style.display = 'none';
                    icon.className = 'fas fa-chevron-down';
                    parentRow.classList.remove('type-row-active');
                }
            });
        });
        
        searchInput.addEventListener('input', filterTable);
    });
</script>
@endpush
@endsection 