@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Detail Kategori Risiko</h1>
        </div>
    </div>

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

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informasi Kategori Risiko</h5>
                    <div>
                        <a href="{{ route('risk-management.settings.categories.edit', $category->id) }}" class="btn btn-sm btn-light">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th class="w-25">Kode</th>
                                    <td>{{ $category->code ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ $category->name }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori Induk</th>
                                    <td>{{ $category->parent ? $category->parent->name : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Urutan</th>
                                    <td>{{ $category->order ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Deskripsi</th>
                                    <td>{{ $category->description ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th class="w-25">Dibuat Oleh</th>
                                    <td>{{ $category->creator ? $category->creator->name : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Dibuat</th>
                                    <td>{{ $category->created_at ? $category->created_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Diupdate Oleh</th>
                                    <td>{{ $category->updater ? $category->updater->name : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Update</th>
                                    <td>{{ $category->updated_at ? $category->updated_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('risk-management.settings.categories.list') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-1"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>

            @if($category->children && $category->children->count() > 0)
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Daftar Sub Kategori</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->children as $child)
                                <tr>
                                    <td>{{ $child->code ?? '-' }}</td>
                                    <td>{{ $child->name }}</td>
                                    <td>
                                        @if($child->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('risk-management.settings.categories.show', $child->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kategori <strong>{{ $category->name }}</strong>?</p>
                <p>Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('risk-management.settings.categories.destroy', $category->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 