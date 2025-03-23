@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Dashboard Manajemen Risiko</h1>
                <div>
                    <a href="{{ route('risk-management.incidents.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Laporkan Insiden Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">Filter Data</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('risk-management.dashboard.index') }}" method="GET" id="filterForm">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Periode</label>
                                <div class="input-group">
                                    <input type="month" class="form-control" name="start_month" value="{{ request('start_month', date('Y-m', strtotime('-5 months'))) }}">
                                    <span class="input-group-text">s/d</span>
                                    <input type="month" class="form-control" name="end_month" value="{{ request('end_month', date('Y-m')) }}">
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="location" class="form-label">Lokasi</label>
                                <select class="form-select" id="location" name="location">
                                    <option value="">Semua Lokasi</option>
                                    @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ request('location') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="incident_type" class="form-label">Jenis Insiden</label>
                                <select class="form-select" id="incident_type" name="incident_type">
                                    <option value="">Semua Jenis</option>
                                    @foreach($incidentTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('incident_type') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="risk_level" class="form-label">Tingkat Risiko</label>
                                <select class="form-select" id="risk_level" name="risk_level">
                                    <option value="">Semua Tingkat</option>
                                    @foreach($riskLevels as $level)
                                    <option value="{{ $level->id }}" {{ request('risk_level') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Terapkan Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="card shadow-sm border-primary h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Insiden</h5>
                    <div class="display-4 fw-bold text-primary">{{ $totalIncidents }}</div>
                    <div class="text-muted small">Periode {{ $startDate->format('M Y') }} - {{ $endDate->format('M Y') }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="card shadow-sm h-100">
                <div class="card-body p-0">
                    <div class="p-3 border-bottom">
                        <h5 class="card-title mb-0">Berdasarkan Status</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-file-alt text-primary me-2"></i> Dilaporkan</span>
                            <span class="badge bg-primary rounded-pill">{{ $incidentsByStatus['reported'] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-search text-info me-2"></i> Investigasi</span>
                            <span class="badge bg-info rounded-pill">{{ $incidentsByStatus['investigating'] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-clipboard-check text-warning me-2"></i> Dianalisis</span>
                            <span class="badge bg-warning rounded-pill">{{ $incidentsByStatus['analyzed'] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-check-circle text-success me-2"></i> Diselesaikan</span>
                            <span class="badge bg-success rounded-pill">{{ $incidentsByStatus['resolved'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Distribusi Tingkat Risiko</h5>
                    <div class="chart-container" style="position: relative; height:180px;">
                        <canvas id="riskLevelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card shadow-sm bg-primary text-white text-center h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Insiden</h5>
                    <p class="display-3">{{ $totalIncidents }}</p>
                    <a href="{{ route('risk-management.incidents.index') }}" class="btn btn-light">Lihat Semua</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card shadow-sm bg-info text-white text-center h-100">
                <div class="card-body">
                    <h5 class="card-title">Insiden Baru</h5>
                    <p class="display-3">{{ $incidentsByStatus['reported'] ?? 0 }}</p>
                    <a href="{{ route('risk-management.incidents.index', ['status' => 'reported']) }}" class="btn btn-light">Lihat Insiden Baru</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card shadow-sm bg-warning text-dark text-center h-100">
                <div class="card-body">
                    <h5 class="card-title">Insiden Tingkat Tinggi</h5>
                    <p class="display-3">{{ $highRiskIncidents->count() }}</p>
                    <a href="{{ route('risk-management.incidents.index', ['risk_level' => 'high']) }}" class="btn btn-dark">Tangani Sekarang</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-lg-8 col-md-12 mb-3 mb-lg-0">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Tren Insiden (6 Bulan Terakhir)</h5>
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="incidentTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 mb-3 mb-lg-0">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Insiden Berdasarkan Lokasi</h5>
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="locationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Insiden Berdasarkan Jenis</h5>
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="incidentTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Analisis Akar Permasalahan</h5>
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="rootCauseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Insiden Berisiko Tinggi</h5>
                    <a href="{{ route('risk-management.incidents.index', ['risk_level' => 'high,extreme']) }}" class="btn btn-sm btn-light">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Laporan</th>
                                    <th>Tanggal</th>
                                    <th>Lokasi</th>
                                    <th>Jenis Insiden</th>
                                    <th>Tingkat Risiko</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($highRiskIncidents as $incident)
                                <tr>
                                    <td>{{ $incident->report_number }}</td>
                                    <td>{{ $incident->incident_date->format('d/m/Y') }}</td>
                                    <td>{{ $incident->location->name }}</td>
                                    <td>{{ $incident->incidentType->name }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $incident->riskLevel->color }}; color: {{ $incident->riskLevel->name == 'Rendah' || $incident->riskLevel->name == 'Sedang' ? '#333' : '#fff' }}">
                                            {{ $incident->riskLevel->name }}
                                        </span>
                                    </td>
                                    <td>
                                        @switch($incident->status)
                                            @case('reported')
                                                <span class="badge bg-primary">Dilaporkan</span>
                                                @break
                                            @case('investigating')
                                                <span class="badge bg-info">Investigasi</span>
                                                @break
                                            @case('analyzed')
                                                <span class="badge bg-warning">Dianalisis</span>
                                                @break
                                            @case('resolved')
                                                <span class="badge bg-success">Diselesaikan</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $incident->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('risk-management.incidents.show', $incident->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('risk-management.monitoring.edit', $incident->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3">Tidak ada insiden berisiko tinggi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Risk Level Distribution Chart
        const riskLevelCtx = document.getElementById('riskLevelChart').getContext('2d');
        const riskLevelData = @json($riskLevelData);
        
        new Chart(riskLevelCtx, {
            type: 'doughnut',
            data: {
                labels: riskLevelData.labels,
                datasets: [{
                    data: riskLevelData.data,
                    backgroundColor: riskLevelData.colors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 15,
                            padding: 10
                        }
                    }
                }
            }
        });
        
        // Incident Trend Chart
        const trendCtx = document.getElementById('incidentTrendChart').getContext('2d');
        const trendData = @json($trendData);
        
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendData.months,
                datasets: [{
                    label: 'Jumlah Insiden',
                    data: trendData.counts,
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    tension: 0.3,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4361ee',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Location Chart
        const locationCtx = document.getElementById('locationChart').getContext('2d');
        const locationData = @json($locationData);
        
        new Chart(locationCtx, {
            type: 'pie',
            data: {
                labels: locationData.labels,
                datasets: [{
                    data: locationData.data,
                    backgroundColor: [
                        '#4cc9f0', '#4361ee', '#3a0ca3', '#7209b7', 
                        '#f72585', '#4f772d', '#31572c', '#132a13'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Incident Type Chart
        const typeCtx = document.getElementById('incidentTypeChart').getContext('2d');
        const typeData = @json($typeData);
        
        new Chart(typeCtx, {
            type: 'bar',
            data: {
                labels: typeData.labels,
                datasets: [{
                    label: 'Jumlah Insiden',
                    data: typeData.data,
                    backgroundColor: '#4895ef'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Root Cause Chart
        const rootCauseCtx = document.getElementById('rootCauseChart').getContext('2d');
        const rootCauseData = @json($rootCauseData);
        
        new Chart(rootCauseCtx, {
            type: 'polarArea',
            data: {
                labels: rootCauseData.labels,
                datasets: [{
                    data: rootCauseData.data,
                    backgroundColor: [
                        '#ef476f', '#ffd166', '#06d6a0', '#118ab2', 
                        '#073b4c', '#8d99ae', '#2b2d42'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>
@endpush
@endsection 