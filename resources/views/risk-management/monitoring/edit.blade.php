@extends('layouts.app')

@section('title', 'Monitoring & Penanganan Insiden | Manajemen Risiko')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Monitoring & Penanganan Insiden</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('risk-management.dashboard') }}">Manajemen Risiko</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('risk-management.monitoring.index') }}">Monitoring Tindakan</a></li>
                    <li class="breadcrumb-item active">{{ $incident->status == 'Selesai' ? 'Detail Penanganan' : 'Edit Penanganan' }}</li>
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
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tasks mr-1"></i>
                            {{ $incident->status == 'Selesai' ? 'Detail Penanganan Insiden #' : 'Monitoring & Penanganan Insiden #' }}{{ $incident->id }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('risk-management.incidents.show', $incident->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-check"></i> Sukses!</h5>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                {{ session('error') }}
                            </div>
                        @endif

                        @if(session('info'))
                            <div class="alert alert-info alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-info"></i> Informasi!</h5>
                                {{ session('info') }}
                            </div>
                        @endif

                        @if($incident->status == 'Selesai')
                            <div class="alert alert-info">
                                <i class="icon fas fa-info-circle"></i> Insiden ini sudah selesai ditangani pada {{ $incident->completed_at->format('d/m/Y H:i') }}. Anda masih dapat mengedit data penanganan jika diperlukan.
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Informasi Insiden</h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 200px">ID Insiden</th>
                                                <td>{{ $incident->id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal & Waktu Kejadian</th>
                                                <td>{{ $incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Lokasi</th>
                                                <td>{{ $incident->location->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Jenis Insiden</th>
                                                <td>{{ $incident->incidentType->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Nama Pasien</th>
                                                <td>{{ $incident->nama_pasien }}</td>
                                            </tr>
                                            <tr>
                                                <th>No. Rekam Medis</th>
                                                <td>{{ $incident->no_rm }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    @php
                                                        $statusClass = '';
                                                        switch($incident->status) {
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
                                                        {{ $incident->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-outline card-danger">
                                    <div class="card-header">
                                        <h3 class="card-title">Klasifikasi Risiko</h3>
                                    </div>
                                    <div class="card-body">
                                        @if($incident->classification)
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th style="width: 200px">Tingkat Keparahan</th>
                                                    <td>{{ $incident->classification->dampak ?? '-' }} - {{ $incident->classification->dampak_detail ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Probabilitas</th>
                                                    <td>{{ $incident->classification->probabilitas ?? '-' }} - {{ $incident->classification->probabilitas_detail ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tingkat Risiko</th>
                                                    <td>
                                                        @php
                                                            $riskLevel = $incident->classification->level_risiko ?? '-';
                                                            $badgeClass = '';
                                                            if ($incident->classification) {
                                                                switch($incident->classification->zona_risiko) {
                                                                    case 'Merah':
                                                                        $badgeClass = 'bg-danger';
                                                                        break;
                                                                    case 'Kuning':
                                                                        $badgeClass = 'bg-warning';
                                                                        break;
                                                                    case 'Hijau':
                                                                        $badgeClass = 'bg-success';
                                                                        break;
                                                                    default:
                                                                        $badgeClass = 'bg-info';
                                                                }
                                                            }
                                                        @endphp
                                                        @if($incident->classification)
                                                        <span class="badge {{ $badgeClass }}">
                                                            {{ $riskLevel }} ({{ $incident->classification->zona_risiko ?? '-' }})
                                                        </span>
                                                        @else
                                                        -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Skor Risiko</th>
                                                    <td>{{ $incident->classification->skor_risiko ?? '-' }}</td>
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
                                <div class="card card-outline card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Hasil Analisis Akar Masalah</h3>
                                    </div>
                                    <div class="card-body">
                                        @if($incident->rootCauseAnalysis)
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Akar Masalah:</label>
                                                        <p>{!! nl2br(e($incident->rootCauseAnalysis->root_causes)) !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Rekomendasi Tindakan Perbaikan:</label>
                                                        <p>{!! nl2br(e($incident->rootCauseAnalysis->recommendations)) !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i> Insiden ini belum dianalisis
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Form Penanganan Insiden</h3>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('risk-management.monitoring.update', $incident->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="handling_date">Tanggal Penanganan <span class="text-danger">*</span></label>
                                                        <input type="date" name="handling_date" id="handling_date" class="form-control @error('handling_date') is-invalid @enderror" value="{{ old('handling_date', $incident->handling_date ? $incident->handling_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                                                        @error('handling_date')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="status">Status Penanganan <span class="text-danger">*</span></label>
                                                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                                            <option value="">-- Pilih Status --</option>
                                                            <option value="Dianalisis" {{ old('status', $incident->status) == 'Dianalisis' ? 'selected' : '' }}>Dianalisis</option>
                                                            <option value="Ditangani" {{ old('status', $incident->status) == 'Ditangani' ? 'selected' : '' }}>Ditangani</option>
                                                            <option value="Monitoring" {{ old('status', $incident->status) == 'Monitoring' ? 'selected' : '' }}>Monitoring</option>
                                                            <option value="Selesai" {{ old('status', $incident->status) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                                        </select>
                                                        @error('status')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="handling_actions">Tindakan Penanganan <span class="text-danger">*</span></label>
                                                        <textarea name="handling_actions" id="handling_actions" rows="3" class="form-control @error('handling_actions') is-invalid @enderror" placeholder="Jelaskan tindakan yang dilakukan untuk menangani insiden ini" required>{{ old('handling_actions', $incident->handling_actions) }}</textarea>
                                                        @error('handling_actions')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="handling_result">Hasil Penanganan <span class="text-danger">*</span></label>
                                                        <textarea name="handling_result" id="handling_result" rows="3" class="form-control @error('handling_result') is-invalid @enderror" placeholder="Jelaskan hasil dari penanganan yang telah dilakukan" required>{{ old('handling_result', $incident->handling_result) }}</textarea>
                                                        @error('handling_result')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="follow_up_plan">Rencana Tindak Lanjut</label>
                                                        <textarea name="follow_up_plan" id="follow_up_plan" rows="3" class="form-control @error('follow_up_plan') is-invalid @enderror" placeholder="Jelaskan rencana tindak lanjut (jika ada)">{{ old('follow_up_plan', $incident->follow_up_plan) }}</textarea>
                                                        @error('follow_up_plan')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="handling_document">Dokumen Penanganan</label>
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input" id="handling_document" name="handling_document">
                                                                <label class="custom-file-label" for="handling_document">Pilih file</label>
                                                            </div>
                                                        </div>
                                                        <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG. Maksimal ukuran: 5MB</small>
                                                        @error('handling_document')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        
                                                        @if($incident->handling_document)
                                                        <div class="mt-2">
                                                            <a href="{{ asset('storage/documents/incidents/' . $incident->handling_document) }}" target="_blank" class="btn btn-sm btn-info">
                                                                <i class="fas fa-file-alt"></i> Lihat Dokumen
                                                            </a>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <a href="{{ route('risk-management.incidents.show', $incident->id) }}" class="btn btn-secondary">Batalkan</a>
                                                    <button type="submit" class="btn btn-success float-right">Simpan Penanganan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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

@section('scripts')
<script>
    $(function() {
        // Inisialisasi file input
        bsCustomFileInput.init();
    });
</script>
@endsection 