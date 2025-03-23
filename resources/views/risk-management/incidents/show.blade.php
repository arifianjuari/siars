@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0">Detail Insiden</h5>
                    <div>
                        <a href="{{ route('risk-management.incidents.edit', $incident->id) }}" class="btn btn-sm btn-warning me-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-danger me-2" data-bs-toggle="modal" data-bs-target="#deleteIncidentModal">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                        <a href="{{ route('risk-management.incidents.index') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Status</th>
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
                        </tr>
                        <tr>
                            <th>Tanggal & Waktu Kejadian</th>
                            <td>{{ $incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Lokasi Kejadian</th>
                            <td>{{ $incident->location->name }}</td>
                        </tr>
                        <tr>
                            <th>Nama Pasien / Inisial</th>
                            <td>{{ $incident->nama_pasien }}</td>
                        </tr>
                        <tr>
                            <th>Nomor Rekam Medis</th>
                            <td>{{ $incident->no_rm }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Insiden</th>
                            <td>{{ $incident->incidentType->name }}</td>
                        </tr>
                        <tr>
                            <th>Subtipe Insiden</th>
                            <td>{{ $incident->incidentSubtype ? $incident->incidentSubtype->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Kronologis</th>
                            <td>{{ $incident->kronologis }}</td>
                        </tr>
                        <tr>
                            <th>Tindakan Langsung</th>
                            <td>{{ $incident->tindakan_langsung }}</td>
                        </tr>
                        <tr>
                            <th>Pelapor</th>
                            <td>{{ $incident->reporter->name }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Pelaporan</th>
                            <td>{{ $incident->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Dokumen Pendukung</th>
                            <td>
                                @if($incident->dokumen_pendukung)
                                    <a href="{{ Storage::url($incident->dokumen_pendukung) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-file-download"></i> Lihat Dokumen
                                    </a>
                                @else
                                    <span class="text-muted">Tidak ada dokumen</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Alur Penanganan</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-check-circle text-success me-2"></i> Pelaporan Insiden
                            </span>
                            <span class="badge bg-success rounded-pill">Selesai</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <i class="{{ $incident->classification ? 'fas fa-check-circle text-success' : 'fas fa-circle text-secondary' }} me-2"></i> Klasifikasi Risiko
                            </span>
                            @if($incident->classification)
                                <span class="badge bg-success rounded-pill">Selesai</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">Belum</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <i class="{{ $incident->analysis ? 'fas fa-check-circle text-success' : 'fas fa-circle text-secondary' }} me-2"></i> Analisis Akar Masalah
                            </span>
                            @if($incident->analysis)
                                <span class="badge bg-success rounded-pill">Selesai</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">Belum</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <i class="{{ $incident->handler_id ? 'fas fa-check-circle text-success' : 'fas fa-circle text-secondary' }} me-2"></i> Monitoring & Evaluasi
                            </span>
                            @if($incident->handler_id)
                                <span class="badge bg-success rounded-pill">Selesai</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">Belum</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            @if($incident->classification)
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Hasil Klasifikasi Risiko</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Dampak</th>
                            <td>{{ $incident->classification->dampak }} - {{ $incident->classification->dampak_detail }}</td>
                        </tr>
                        <tr>
                            <th>Probabilitas</th>
                            <td>{{ $incident->classification->probabilitas }} - {{ $incident->classification->probabilitas_detail }}</td>
                        </tr>
                        <tr>
                            <th>Skor Risiko</th>
                            <td>{{ $incident->classification->skor_risiko }}</td>
                        </tr>
                        <tr>
                            <th>Level Risiko</th>
                            <td>
                                <span class="badge bg-{{ $incident->classification->zona_risiko == 'Merah' ? 'danger' : ($incident->classification->zona_risiko == 'Kuning' ? 'warning' : 'success') }}">
                                    {{ $incident->classification->level_risiko }} ({{ $incident->classification->zona_risiko }})
                                </span>
                            </td>
                        </tr>
                    </table>
                    <div class="mt-2 d-flex gap-2">
                        <a href="{{ route('risk-management.classifications.show', $incident->classification->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Lihat Detail Klasifikasi
                        </a>
                        <a href="{{ route('risk-management.classifications.edit', $incident->classification->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit Klasifikasi
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Tindakan</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$incident->classification)
                            <a href="{{ route('risk-management.classifications.create', ['incident_id' => $incident->id]) }}" class="btn btn-warning">
                                <i class="fas fa-clipboard-check"></i> Klasifikasi Insiden
                            </a>
                        @elseif(!$incident->analysis)
                            <div class="d-flex gap-2">
                                <a href="{{ route('risk-management.classifications.edit', $incident->classification->id) }}" class="btn btn-warning flex-grow-1">
                                    <i class="fas fa-edit"></i> Edit Klasifikasi
                                </a>
                                <a href="{{ route('risk-management.analysis.create', ['incident_id' => $incident->id]) }}" class="btn btn-primary flex-grow-1">
                                    <i class="fas fa-search"></i> Analisis Akar Masalah
                                </a>
                            </div>
                        @elseif($incident->analysis && $incident->status != 'Selesai')
                            <div class="d-flex gap-2">
                                <a href="{{ route('risk-management.classifications.edit', $incident->classification->id) }}" class="btn btn-warning flex-grow-1">
                                    <i class="fas fa-edit"></i> Edit Klasifikasi
                                </a>
                                <a href="{{ route('risk-management.monitoring.edit', $incident->id) }}" class="btn btn-info flex-grow-1">
                                    <i class="fas fa-tasks"></i> Monitoring & Evaluasi
                                </a>
                            </div>
                        @endif
                        
                        @if($incident->status == 'Selesai')
                            <a href="{{ route('risk-management.reports.show', $incident->id) }}" class="btn btn-success">
                                <i class="fas fa-file-alt"></i> Lihat Laporan
                            </a>
                            <a href="{{ route('risk-management.incidents.edit', $incident->id) }}" class="btn btn-warning mt-2">
                                <i class="fas fa-edit"></i> Edit Data Insiden
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($incident->analysis)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Hasil Analisis Akar Masalah</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Kronologi Detail</h6>
                            <p>{{ $incident->kronologis }}</p>
                            
                            <h6>Faktor Penyebab</h6>
                            <div class="mb-3">
                                <strong>Metode Analisis:</strong> {{ $incident->analysis->analysis_method }}
                            </div>
                            
                            <div class="accordion" id="accordionFaktor">
                                <!-- Faktor Tim -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faktortimCollapse" aria-expanded="true" aria-controls="faktortimCollapse">
                                            Faktor Tim
                                        </button>
                                    </h2>
                                    <div id="faktortimCollapse" class="accordion-collapse collapse show" data-bs-parent="#accordionFaktor">
                                        <div class="accordion-body">
                                            @if($incident->analysis->team_factors)
                                                <p>{{ $incident->analysis->team_factors }}</p>
                                            @else
                                                <p class="text-muted">Tidak ada faktor tim yang dipilih</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Faktor Sistem -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faktorsistemCollapse" aria-expanded="false" aria-controls="faktorsistemCollapse">
                                            Faktor Sistem
                                        </button>
                                    </h2>
                                    <div id="faktorsistemCollapse" class="accordion-collapse collapse" data-bs-parent="#accordionFaktor">
                                        <div class="accordion-body">
                                            @if($incident->analysis->system_factors)
                                                <p>{{ $incident->analysis->system_factors }}</p>
                                            @else
                                                <p class="text-muted">Tidak ada faktor sistem yang dipilih</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Faktor Pasien -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faktorpasienCollapse" aria-expanded="false" aria-controls="faktorpasienCollapse">
                                            Faktor Pasien
                                        </button>
                                    </h2>
                                    <div id="faktorpasienCollapse" class="accordion-collapse collapse" data-bs-parent="#accordionFaktor">
                                        <div class="accordion-body">
                                            @if($incident->analysis->patient_factors)
                                                <p>{{ $incident->analysis->patient_factors }}</p>
                                            @else
                                                <p class="text-muted">Tidak ada faktor pasien yang dipilih</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Faktor Lingkungan -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faktorlingkunganCollapse" aria-expanded="false" aria-controls="faktorlingkunganCollapse">
                                            Faktor Lingkungan
                                        </button>
                                    </h2>
                                    <div id="faktorlingkunganCollapse" class="accordion-collapse collapse" data-bs-parent="#accordionFaktor">
                                        <div class="accordion-body">
                                            @if($incident->analysis->environmental_factors)
                                                <p>{{ $incident->analysis->environmental_factors }}</p>
                                            @else
                                                <p class="text-muted">Tidak ada faktor lingkungan yang dipilih</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Akar Masalah</h6>
                            <p>{{ $incident->analysis->root_causes }}</p>
                            
                            <h6>Rekomendasi Tindakan Perbaikan</h6>
                            <p>{{ $incident->analysis->recommendations }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('risk-management.analysis.show', ['analysis' => $incident->analysis->id]) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Lihat Detail Analisis
                        </a>
                        <a href="{{ route('risk-management.analysis.edit', ['analysis' => $incident->analysis->id]) }}" class="btn btn-sm btn-warning ms-1">
                            <i class="fas fa-edit"></i> Edit Analisis
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($incident->handling_actions || $incident->handling_date || $incident->handling_result)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Penanganan Insiden</h5>
                    <a href="{{ route('risk-management.monitoring.edit', $incident->id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Edit Penanganan
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="fw-bold">Tanggal Penanganan:</label>
                                <p>{{ $incident->handling_date ? $incident->handling_date->format('d/m/Y') : '-' }}</p>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="fw-bold">Tindakan Penanganan:</label>
                                <p>{{ $incident->handling_actions ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="fw-bold">Hasil Penanganan:</label>
                                <p>{{ $incident->handling_result ?? '-' }}</p>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="fw-bold">Rencana Tindak Lanjut:</label>
                                <p>{{ $incident->follow_up_plan ?? '-' }}</p>
                            </div>
                            
                            @if($incident->handling_document)
                            <div class="form-group">
                                <label class="fw-bold">Dokumen Penanganan:</label>
                                <div>
                                    <a href="{{ asset('storage/documents/incidents/' . $incident->handling_document) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-file-download"></i> Lihat Dokumen
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteIncidentModal" tabindex="-1" aria-labelledby="deleteIncidentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteIncidentModalLabel">Konfirmasi Hapus Insiden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus insiden ini? Tindakan ini tidak dapat dibatalkan dan semua data terkait (klasifikasi, analisis, dan penanganan) juga akan dihapus.</p>
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
@endsection 