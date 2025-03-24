@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">QR Code Verifikasi</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/img/logo.png') }}" alt="Logo" class="img-fluid" style="max-height: 80px;">
                    </div>
                    <div class="alert alert-success text-center">
                        <i class="fas fa-check-circle me-2"></i> QR Code valid untuk insiden keselamatan pasien
                    </div>
                    
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            @if($incident)
                                <tr>
                                    <th class="bg-light">Nomor Kasus</th>
                                    <td><strong>{{ $incident->case_number }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Tanggal Kejadian</th>
                                    <td>{{ $incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Lokasi</th>
                                    <td>{{ $incident->location->name }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Jenis Insiden</th>
                                    <td>{{ $incident->incidentType->name }}</td>
                                </tr>
                            @else
                                <tr>
                                    <th class="bg-light">Status</th>
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
                            @endif
                        </table>
                    </div>
                    
                    <div class="text-center mt-4">
                        <p class="text-muted">QR code ini digunakan untuk memverifikasi insiden keselamatan pasien.</p>
                        <p class="small">Discan pada {{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <p class="small mb-0">SIARS - Sistem Informasi Rumah Sakit</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 