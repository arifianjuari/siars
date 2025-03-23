@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Dashboard Manajemen Risiko Pasien</h1>
                <div>
                    <a href="{{ route('risk-management.incidents.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Laporkan Insiden Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ringkasan Statistik -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="card shadow-sm bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Total Insiden</h5>
                            <h2 class="display-4">{{ $totalIncidents }}</h2>
                        </div>
                        <div class="mt-2">
                            <i class="fas fa-clipboard-list fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-primary border-top border-white border-opacity-25 text-center">
                    <a href="{{ route('risk-management.incidents.index') }}" class="text-white">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="card shadow-sm bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Insiden Baru</h5>
                            <h2 class="display-4">{{ $newIncidents }}</h2>
                        </div>
                        <div class="mt-2">
                            <i class="fas fa-file-medical fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-info border-top border-white border-opacity-25 text-center">
                    <a href="{{ route('risk-management.incidents.index', ['status' => 'Baru']) }}" class="text-white">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="card shadow-sm bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Dalam Proses</h5>
                            <h2 class="display-4">{{ $ongoingIncidents }}</h2>
                        </div>
                        <div class="mt-2">
                            <i class="fas fa-spinner fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-warning border-top border-white border-opacity-25 text-center">
                    <a href="{{ route('risk-management.incidents.index', ['status' => 'Proses']) }}" class="text-white">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="card shadow-sm bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Selesai</h5>
                            <h2 class="display-4">{{ $completedIncidents }}</h2>
                        </div>
                        <div class="mt-2">
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-success border-top border-white border-opacity-25 text-center">
                    <a href="{{ route('risk-management.incidents.index', ['status' => 'Selesai']) }}" class="text-white">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Insiden Risiko Tinggi yang Memerlukan Penanganan -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Insiden Risiko Tinggi yang Memerlukan Penanganan Segera</h5>
                    <a href="{{ route('risk-management.incidents.index', ['level_risiko' => 'Tinggi']) }}" class="btn btn-sm btn-outline-light">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Lokasi</th>
                                    <th>No RM</th>
                                    <th>Jenis Insiden</th>
                                    <th>Skor Risiko</th>
                                    <th>Status</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($highRiskIncidents as $incident)
                                <tr>
                                    <td>{{ $incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</td>
                                    <td>{{ $incident->location->name }}</td>
                                    <td>{{ $incident->no_rm }}</td>
                                    <td>{{ $incident->incidentType->name }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ $incident->classification->skor_risiko }}</span>
                                    </td>
                                    <td>
                                        @if($incident->status == 'Baru')
                                            <span class="badge bg-info">Baru</span>
                                        @elseif($incident->status == 'Proses')
                                            <span class="badge bg-primary">Dalam Proses</span>
                                        @elseif($incident->status == 'Evaluasi')
                                            <span class="badge bg-warning">Evaluasi</span>
                                        @elseif($incident->status == 'Selesai')
                                            <span class="badge bg-success">Selesai</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('risk-management.monitoring.edit', $incident->id) }}" class="btn btn-sm btn-danger">Tangani</a>
                                        <button type="button" class="btn btn-sm btn-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteModalHigh{{ $incident->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        
                                        <!-- Modal Konfirmasi Hapus -->
                                        <div class="modal fade" id="deleteModalHigh{{ $incident->id }}" tabindex="-1" aria-labelledby="deleteModalHighLabel{{ $incident->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deleteModalHighLabel{{ $incident->id }}">Konfirmasi Hapus Insiden</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Apakah Anda yakin ingin menghapus insiden berisiko tinggi ini?</p>
                                                        <p><strong>Tanggal:</strong> {{ $incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</p>
                                                        <p><strong>Lokasi:</strong> {{ $incident->location->name }}</p>
                                                        <p><strong>No. RM:</strong> {{ $incident->no_rm }}</p>
                                                        <p class="text-danger">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait (klasifikasi, analisis, dan penanganan).</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <form action="{{ route('risk-management.incidents.destroy', $incident->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Hapus Insiden</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3">
                                        <p class="text-muted mb-0">Tidak ada insiden risiko tinggi yang memerlukan penanganan segera</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Insiden Terbaru & Tindakan Cepat -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0">Insiden Terbaru</h5>
                    <div>
                        <a href="{{ route('risk-management.incidents.index') }}" class="btn btn-outline-secondary me-2">Lihat Semua Insiden</a>
                        <a href="{{ route('risk-management.incidents.create') }}" class="btn btn-primary">Laporkan Insiden Baru</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Lokasi</th>
                                    <th>No RM</th>
                                    <th>Jenis Insiden</th>
                                    <th>Level Risiko</th>
                                    <th>Status</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentIncidents as $incident)
                                <tr>
                                    <td>{{ $incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</td>
                                    <td>{{ $incident->location->name }}</td>
                                    <td>{{ $incident->no_rm }}</td>
                                    <td>{{ $incident->incidentType->name }}</td>
                                    <td>
                                        @if($incident->classification)
                                            <span class="badge bg-{{ $incident->classification->zona_risiko == 'Merah' ? 'danger' : ($incident->classification->zona_risiko == 'Kuning' ? 'warning' : 'success') }}">
                                                {{ $incident->classification->level_risiko }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Belum Diklasifikasi</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($incident->status == 'Baru')
                                            <span class="badge bg-info">Baru</span>
                                        @elseif($incident->status == 'Proses')
                                            <span class="badge bg-primary">Dalam Proses</span>
                                        @elseif($incident->status == 'Evaluasi')
                                            <span class="badge bg-warning">Evaluasi</span>
                                        @elseif($incident->status == 'Selesai')
                                            <span class="badge bg-success">Selesai</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('risk-management.incidents.show', $incident->id) }}" class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$incident->classification)
                                                <a href="{{ route('risk-management.classifications.create', ['incident_id' => $incident->id]) }}" class="btn btn-sm btn-warning" title="Klasifikasi">
                                                    <i class="fas fa-clipboard-check"></i>
                                                </a>
                                            @endif
                                            @if($incident->classification && !$incident->analysis)
                                                <a href="{{ route('risk-management.analysis.create', ['incident_id' => $incident->id]) }}" class="btn btn-sm btn-primary" title="Analisis">
                                                    <i class="fas fa-search"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('risk-management.monitoring.edit', $incident->id) }}" class="btn btn-sm btn-secondary" title="Monitoring">
                                                <i class="fas fa-tasks"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $incident->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Modal Konfirmasi Hapus -->
                                <div class="modal fade" id="deleteModal{{ $incident->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $incident->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $incident->id }}">Konfirmasi Hapus Insiden</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menghapus insiden ini?</p>
                                                <p><strong>Tanggal:</strong> {{ $incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</p>
                                                <p><strong>Lokasi:</strong> {{ $incident->location->name }}</p>
                                                <p><strong>No. RM:</strong> {{ $incident->no_rm }}</p>
                                                <p class="text-danger">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait (klasifikasi, analisis, dan penanganan).</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('risk-management.incidents.destroy', $incident->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Hapus Insiden</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                                            <h5>Belum ada insiden yang dilaporkan</h5>
                                            <p class="text-muted">Klik tombol "Laporkan Insiden Baru" untuk membuat laporan insiden baru</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Row untuk Aksi Cepat dan Statistik Risiko -->
    <div class="row mb-4">
        <div class="col-md-4 mb-4 mb-md-0">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('risk-management.incidents.create') }}" class="btn btn-primary btn-lg mb-2">
                            <i class="fas fa-plus-circle me-2"></i> Laporkan Insiden Baru
                        </a>
                        <a href="{{ route('risk-management.incidents.index', ['level_risiko' => 'Tinggi']) }}" class="btn btn-danger mb-2">
                            <i class="fas fa-exclamation-triangle me-2"></i> Tangani Insiden Risiko Tinggi
                        </a>
                        <a href="{{ route('risk-management.incidents.index', ['status' => 'Evaluasi']) }}" class="btn btn-warning mb-2">
                            <i class="fas fa-clipboard-check me-2"></i> Insiden dalam Evaluasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Statistik Level Risiko</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 col-6 mb-3 mb-md-0">
                            <div class="card border-success mb-3">
                                <div class="card-body p-2">
                                    <h3 class="text-success mb-0">{{ $lowRiskCount }}</h3>
                                </div>
                                <div class="card-footer bg-success p-1">
                                    <small class="text-white">Rendah</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3 mb-md-0">
                            <div class="card border-warning mb-3">
                                <div class="card-body p-2">
                                    <h3 class="text-warning mb-0">{{ $mediumRiskCount }}</h3>
                                </div>
                                <div class="card-footer bg-warning p-1">
                                    <small class="text-white">Sedang</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3 mb-md-0">
                            <div class="card border-danger mb-3">
                                <div class="card-body p-2">
                                    <h3 class="text-danger mb-0">{{ $highRiskCount }}</h3>
                                </div>
                                <div class="card-footer bg-danger p-1">
                                    <small class="text-white">Tinggi</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3 mb-md-0">
                            <div class="card border-dark mb-3">
                                <div class="card-body p-2">
                                    <h3 class="text-dark mb-0">{{ $extremeRiskCount }}</h3>
                                </div>
                                <div class="card-footer bg-dark p-1">
                                    <small class="text-white">Ekstrim</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 25px;">
                        @if($totalClassifiedIncidents > 0)
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($lowRiskCount / $totalClassifiedIncidents) * 100 }}%" 
                                aria-valuenow="{{ $lowRiskCount }}" aria-valuemin="0" aria-valuemax="{{ $totalClassifiedIncidents }}">
                                {{ round(($lowRiskCount / $totalClassifiedIncidents) * 100) }}%
                            </div>
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ ($mediumRiskCount / $totalClassifiedIncidents) * 100 }}%" 
                                aria-valuenow="{{ $mediumRiskCount }}" aria-valuemin="0" aria-valuemax="{{ $totalClassifiedIncidents }}">
                                {{ round(($mediumRiskCount / $totalClassifiedIncidents) * 100) }}%
                            </div>
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ ($highRiskCount / $totalClassifiedIncidents) * 100 }}%" 
                                aria-valuenow="{{ $highRiskCount }}" aria-valuemin="0" aria-valuemax="{{ $totalClassifiedIncidents }}">
                                {{ round(($highRiskCount / $totalClassifiedIncidents) * 100) }}%
                            </div>
                            <div class="progress-bar bg-dark" role="progressbar" style="width: {{ ($extremeRiskCount / $totalClassifiedIncidents) * 100 }}%" 
                                aria-valuenow="{{ $extremeRiskCount }}" aria-valuemin="0" aria-valuemax="{{ $totalClassifiedIncidents }}">
                                {{ round(($extremeRiskCount / $totalClassifiedIncidents) * 100) }}%
                            </div>
                        @else
                            <div class="progress-bar bg-secondary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                Belum ada data
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grafik dan Analitik -->
    <div class="row">
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Jumlah Insiden per Bulan</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyIncidentsChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Jenis Insiden</h5>
                </div>
                <div class="card-body">
                    <canvas id="incidentTypesChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Distribusi Lokasi dan Status Penanganan -->
    <div class="row">
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Distribusi per Lokasi</h5>
                </div>
                <div class="card-body">
                    <canvas id="locationChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Status Penanganan</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="300"></canvas>
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
    // Inisialisasi grafik bulanan
    const monthlyCtx = document.getElementById('monthlyIncidentsChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyLabels) !!},
            datasets: [{
                label: 'Jumlah Insiden',
                data: {!! json_encode($monthlyData) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    // Inisialisasi grafik jenis insiden
    const typesCtx = document.getElementById('incidentTypesChart').getContext('2d');
    const typesChart = new Chart(typesCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($incidentTypeLabels) !!},
            datasets: [{
                data: {!! json_encode($incidentTypeData) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
    
    // Inisialisasi grafik distribusi lokasi
    const locationCtx = document.getElementById('locationChart').getContext('2d');
    const locationChart = new Chart(locationCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($locationLabels) !!},
            datasets: [{
                label: 'Jumlah Insiden',
                data: {!! json_encode($locationData) !!},
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    // Inisialisasi grafik status penanganan
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statusLabels) !!},
            datasets: [{
                data: {!! json_encode($statusData) !!},
                backgroundColor: [
                    'rgba(23, 162, 184, 0.6)', // Baru
                    'rgba(0, 123, 255, 0.6)',  // Dalam Proses
                    'rgba(255, 193, 7, 0.6)',  // Evaluasi
                    'rgba(40, 167, 69, 0.6)'   // Selesai
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
</script>
@endpush
@endsection 