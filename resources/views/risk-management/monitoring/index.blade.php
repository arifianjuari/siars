@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('risk-management.dashboard') }}">Manajemen Risiko</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Monitoring Insiden</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Monitoring Penanganan Insiden</h1>
            </div>
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

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Filter Insiden</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('risk-management.monitoring.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="Dianalisis" {{ request('status') == 'Dianalisis' ? 'selected' : '' }}>Dianalisis</option>
                                    <option value="Ditangani" {{ request('status') == 'Ditangani' ? 'selected' : '' }}>Ditangani</option>
                                    <option value="Monitoring" {{ request('status') == 'Monitoring' ? 'selected' : '' }}>Monitoring</option>
                                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="level_risiko" class="form-label">Level Risiko</label>
                                <select class="form-select" id="level_risiko" name="level_risiko">
                                    <option value="">Semua Level</option>
                                    <option value="Rendah" {{ request('level_risiko') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                                    <option value="Sedang" {{ request('level_risiko') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                                    <option value="Tinggi" {{ request('level_risiko') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                                    <option value="Ekstrim" {{ request('level_risiko') == 'Ekstrim' ? 'selected' : '' }}>Ekstrim</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                                <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('risk-management.monitoring.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-eraser me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Daftar Insiden untuk Monitoring</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal Insiden</th>
                                    <th>Kode</th>
                                    <th>Lokasi</th>
                                    <th>Jenis Insiden</th>
                                    <th>Level Risiko</th>
                                    <th>Status</th>
                                    <th>Tanggal Update</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($incidents as $incident)
                                <tr>
                                    <td>{{ $incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</td>
                                    <td>{{ $incident->kode }}</td>
                                    <td>{{ $incident->location->name }}</td>
                                    <td>{{ $incident->type->name }}</td>
                                    <td>
                                        @if($incident->classification)
                                            <span class="badge bg-{{ $incident->classification->level_risiko == 'Tinggi' || $incident->classification->level_risiko == 'Ekstrim' ? 'danger' : ($incident->classification->level_risiko == 'Sedang' ? 'warning' : 'success') }}">
                                                {{ $incident->classification->level_risiko }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Belum Diklasifikasi</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($incident->status == 'Baru')
                                            <span class="badge bg-info">Baru</span>
                                        @elseif($incident->status == 'Diklasifikasi')
                                            <span class="badge bg-info">Diklasifikasi</span>
                                        @elseif($incident->status == 'Dianalisis')
                                            <span class="badge bg-primary">Dianalisis</span>
                                        @elseif($incident->status == 'Ditangani')
                                            <span class="badge bg-primary">Ditangani</span>
                                        @elseif($incident->status == 'Monitoring')
                                            <span class="badge bg-warning">Monitoring</span>
                                        @elseif($incident->status == 'Selesai')
                                            <span class="badge bg-success">Selesai</span>
                                        @endif
                                    </td>
                                    <td>{{ $incident->updated_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('risk-management.incidents.show', $incident->id) }}" class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($incident->status != 'Selesai')
                                                <a href="{{ route('risk-management.monitoring.edit', $incident->id) }}" class="btn btn-sm btn-primary" title="Update Status">
                                                    <i class="fas fa-tasks"></i>
                                                </a>
                                            @endif
                                            
                                            <a href="{{ route('risk-management.monitoring.edit', $incident->id) }}" class="btn btn-sm btn-warning" title="Edit Penanganan">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                            <h5>Belum ada insiden yang dianalisis</h5>
                                            <p class="text-muted">Insiden yang sudah dianalisis akar masalahnya akan muncul di sini</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $incidents->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        transition: all 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endpush
@endsection 