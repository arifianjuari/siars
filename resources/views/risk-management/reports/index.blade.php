@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Laporan Manajemen Risiko</h1>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Jenis Laporan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Laporan Matriks Risiko -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm border-primary">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="icon-circle bg-primary text-white me-3">
                                            <i class="fas fa-chart-line"></i>
                                        </span>
                                        <h5 class="card-title mb-0">Matriks Risiko</h5>
                                    </div>
                                    <p class="card-text text-muted mb-4">Visualisasi matriks risiko berdasarkan dampak dan probabilitas.</p>
                                </div>
                                <div class="card-footer bg-white border-0 text-end">
                                    <a href="{{ route('risk-management.reports.show', 'matrix') }}" class="btn btn-primary">
                                        <i class="fas fa-file-alt me-1"></i> Lihat Laporan
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Laporan Risiko Per Kategori -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm border-success">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="icon-circle bg-success text-white me-3">
                                            <i class="fas fa-chart-pie"></i>
                                        </span>
                                        <h5 class="card-title mb-0">Risiko Per Kategori</h5>
                                    </div>
                                    <p class="card-text text-muted mb-4">Laporan jumlah dan tingkat risiko per kategori.</p>
                                </div>
                                <div class="card-footer bg-white border-0 text-end">
                                    <a href="{{ route('risk-management.reports.show', 'category') }}" class="btn btn-success">
                                        <i class="fas fa-file-alt me-1"></i> Lihat Laporan
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Laporan Tren Risiko -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm border-purple">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="icon-circle bg-purple text-white me-3">
                                            <i class="fas fa-chart-area"></i>
                                        </span>
                                        <h5 class="card-title mb-0">Tren Risiko</h5>
                                    </div>
                                    <p class="card-text text-muted mb-4">Tren perubahan nilai risiko dari waktu ke waktu.</p>
                                </div>
                                <div class="card-footer bg-white border-0 text-end">
                                    <a href="{{ route('risk-management.reports.show', 'trend') }}" class="btn btn-purple">
                                        <i class="fas fa-file-alt me-1"></i> Lihat Laporan
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Laporan Rekomendasi Perbaikan -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm border-danger">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="icon-circle bg-danger text-white me-3">
                                            <i class="fas fa-clipboard-list"></i>
                                        </span>
                                        <h5 class="card-title mb-0">Rekomendasi Perbaikan</h5>
                                    </div>
                                    <p class="card-text text-muted mb-4">Laporan tentang rekomendasi perbaikan berdasarkan analisis insiden.</p>
                                </div>
                                <div class="card-footer bg-white border-0 text-end">
                                    <a href="{{ route('risk-management.reports.show', 'recommendations') }}" class="btn btn-danger">
                                        <i class="fas fa-file-alt me-1"></i> Lihat Laporan
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Laporan Penanganan Insiden -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm border-info">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="icon-circle bg-info text-white me-3">
                                            <i class="fas fa-tasks"></i>
                                        </span>
                                        <h5 class="card-title mb-0">Penanganan Insiden</h5>
                                    </div>
                                    <p class="card-text text-muted mb-4">Status penanganan insiden dan evaluasi tindakan perbaikan.</p>
                                </div>
                                <div class="card-footer bg-white border-0 text-end">
                                    <a href="{{ route('risk-management.reports.show', 'handling') }}" class="btn btn-info text-white">
                                        <i class="fas fa-file-alt me-1"></i> Lihat Laporan
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Laporan Kinerja Tim -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm border-warning">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="icon-circle bg-warning text-white me-3">
                                            <i class="fas fa-users"></i>
                                        </span>
                                        <h5 class="card-title mb-0">Kinerja Tim</h5>
                                    </div>
                                    <p class="card-text text-muted mb-4">Laporan kinerja tim dalam penanganan insiden dan manajemen risiko.</p>
                                </div>
                                <div class="card-footer bg-white border-0 text-end">
                                    <a href="{{ route('risk-management.reports.show', 'team') }}" class="btn btn-warning text-white">
                                        <i class="fas fa-file-alt me-1"></i> Lihat Laporan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter dan Export -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pengaturan Laporan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('risk-management.reports.export') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', now()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="report_type" class="form-label">Jenis Laporan</label>
                                <select class="form-select" id="report_type" name="report_type">
                                    <option value="all">Semua Laporan</option>
                                    <option value="matrix">Matriks Risiko</option>
                                    <option value="category">Risiko Per Kategori</option>
                                    <option value="trend">Tren Risiko</option>
                                    <option value="recommendations">Rekomendasi Perbaikan</option>
                                    <option value="handling">Penanganan Insiden</option>
                                    <option value="team">Kinerja Tim</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="export_format" class="form-label">Format Export</label>
                                <select class="form-select" id="export_format" name="export_format">
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                    <option value="csv">CSV</option>
                                </select>
                            </div>
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-file-export me-1"></i> Export Laporan
                                </button>
                            </div>
                        </div>
                    </form>
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
    .icon-circle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 50%;
    }
    .border-purple {
        border-color: #6f42c1;
    }
    .bg-purple {
        background-color: #6f42c1;
    }
    .btn-purple {
        background-color: #6f42c1;
        border-color: #6f42c1;
        color: white;
    }
    .btn-purple:hover {
        background-color: #5a32a3;
        border-color: #5a32a3;
        color: white;
    }
</style>
@endpush
@endsection 