@extends('layouts.app')

@section('title', 'Detail Analisis Akar Masalah | Manajemen Risiko')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Detail Analisis Akar Masalah</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('risk-management.dashboard') }}">Manajemen Risiko</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('risk-management.analysis.index') }}">Analisis Akar Masalah</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-search mr-1"></i>
                            Detail Analisis Akar Masalah Insiden
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('risk-management.analysis.edit', ['analysis' => $analysis->id]) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit Analisis
                            </a>
                            <a href="{{ route('risk-management.monitoring.edit', $analysis->incident_id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-tasks"></i> Monitoring & Penanganan
                            </a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Informasi Insiden</h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 200px">ID Insiden</th>
                                                <td>{{ $analysis->incident->id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal & Waktu Kejadian</th>
                                                <td>{{ $analysis->incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Lokasi</th>
                                                <td>{{ $analysis->incident->location->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Jenis Insiden</th>
                                                <td>{{ $analysis->incident->incidentType->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Nama Pasien</th>
                                                <td>{{ $analysis->incident->nama_pasien }}</td>
                                            </tr>
                                            <tr>
                                                <th>No. Rekam Medis</th>
                                                <td>{{ $analysis->incident->no_rm }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    @php
                                                        $statusClass = '';
                                                        switch($analysis->incident->status) {
                                                            case 'Baru':
                                                                $statusClass = 'bg-info';
                                                                break;
                                                            case 'Dianalisis':
                                                                $statusClass = 'bg-primary';
                                                                break;
                                                            case 'Ditangani':
                                                                $statusClass = 'bg-warning';
                                                                break;
                                                            case 'Monitoring':
                                                                $statusClass = 'bg-secondary';
                                                                break;
                                                            case 'Selesai':
                                                                $statusClass = 'bg-success';
                                                                break;
                                                            default:
                                                                $statusClass = 'bg-dark';
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $statusClass }}">
                                                        {{ $analysis->incident->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <h3 class="card-title">Klasifikasi Risiko</h3>
                                    </div>
                                    <div class="card-body">
                                        @if($analysis->incident->classification)
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th style="width: 200px">Tingkat Keparahan</th>
                                                    <td>{{ $analysis->incident->classification->severity ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Probabilitas</th>
                                                    <td>{{ $analysis->incident->classification->probability ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tingkat Risiko</th>
                                                    <td>
                                                        @php
                                                            $riskLevel = $analysis->incident->classification->risk_level;
                                                            $badgeClass = '';
                                                            switch($riskLevel) {
                                                                case 'Rendah':
                                                                    $badgeClass = 'bg-success';
                                                                    break;
                                                                case 'Sedang':
                                                                    $badgeClass = 'bg-warning';
                                                                    break;
                                                                case 'Tinggi':
                                                                    $badgeClass = 'bg-danger';
                                                                    break;
                                                                case 'Ekstrem':
                                                                    $badgeClass = 'bg-dark';
                                                                    break;
                                                                default:
                                                                    $badgeClass = 'bg-info';
                                                            }
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }}">
                                                            {{ $riskLevel }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Skor Risiko</th>
                                                    <td>{{ $analysis->incident->classification->risk_score ?? '-' }}</td>
                                                </tr>
                                            </table>
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i> Insiden ini belum diklasifikasi
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Kronologis Insiden</h3>
                                    </div>
                                    <div class="card-body">
                                        <p>{!! nl2br(e($analysis->incident->kronologis)) !!}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">Analisis Akar Masalah</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Metode Analisis</label>
                                                    <p class="font-weight-bold">{{ $analysis->analysis_method }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Dianalisis oleh</label>
                                                    <p class="font-weight-bold">{{ optional($analysis->analyzedBy)->name ?? '-' }} ({{ $analysis->analyzed_at->format('d/m/Y') }})</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div class="card card-outline card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Akar Masalah</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <p>{!! nl2br(e($analysis->root_causes)) !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <h5 class="font-weight-bold mb-3">Faktor Kontributor</h5>
                                                <div class="card card-outline card-secondary">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Faktor Tim</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <p>{!! nl2br(e($analysis->team_factors ?: 'Tidak ada faktor tim yang teridentifikasi.')) !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div class="card card-outline card-secondary">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Faktor Sistem</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <p>{!! nl2br(e($analysis->system_factors ?: 'Tidak ada faktor sistem yang teridentifikasi.')) !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div class="card card-outline card-secondary">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Faktor Pasien</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <p>{!! nl2br(e($analysis->patient_factors ?: 'Tidak ada faktor pasien yang teridentifikasi.')) !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div class="card card-outline card-secondary">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Faktor Lingkungan</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <p>{!! nl2br(e($analysis->environmental_factors ?: 'Tidak ada faktor lingkungan yang teridentifikasi.')) !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div class="card card-outline card-primary">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Rekomendasi Tindakan Perbaikan</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <p>{!! nl2br(e($analysis->recommendations)) !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <a href="{{ route('risk-management.analysis.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <a href="{{ route('risk-management.monitoring.edit', $analysis->incident_id) }}" class="btn btn-primary float-right">
                                    <i class="fas fa-tasks"></i> Monitoring & Penanganan
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection
