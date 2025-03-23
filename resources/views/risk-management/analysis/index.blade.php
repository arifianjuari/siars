@extends('layouts.app')

@section('title', 'Daftar Analisis Akar Masalah | Manajemen Risiko')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Daftar Analisis Akar Masalah</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('risk-management.dashboard') }}">Manajemen Risiko</a></li>
                    <li class="breadcrumb-item active">Analisis Akar Masalah</li>
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
                        <h3 class="card-title">Analisis Akar Masalah Insiden</h3>
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

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Kode Insiden</th>
                                        <th>Tanggal Kejadian</th>
                                        <th>Lokasi</th>
                                        <th>Jenis Insiden</th>
                                        <th>Tingkat Risiko</th>
                                        <th>Metode Analisis</th>
                                        <th>Tanggal Analisis</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analyses as $key => $analysis)
                                        <tr>
                                            <td>{{ $analyses->firstItem() + $key }}</td>
                                            <td>{{ $analysis->incident->id }}</td>
                                            <td>{{ $analysis->incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</td>
                                            <td>{{ $analysis->incident->location->name }}</td>
                                            <td>{{ $analysis->incident->incidentType->name }}</td>
                                            <td>
                                                @if($analysis->incident->classification)
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
                                                @else
                                                    <span class="badge bg-secondary">Belum diklasifikasi</span>
                                                @endif
                                            </td>
                                            <td>{{ $analysis->analysis_method }}</td>
                                            <td>{{ $analysis->analyzed_at->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('risk-management.analysis.edit', ['analysis' => $analysis->id]) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('risk-management.analysis.show', ['analysis' => $analysis->id]) }}" class="btn btn-sm btn-info" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('risk-management.monitoring.edit', $analysis->incident_id) }}" class="btn btn-sm btn-primary" title="Monitoring">
                                                        <i class="fas fa-tasks"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">Tidak ada data analisis akar masalah</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $analyses->links() }}
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
