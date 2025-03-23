@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Pengaturan Matriks Risiko</h1>
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

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Matriks Risiko</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center align-middle" rowspan="2">Probabilitas</th>
                                    <th class="text-center" colspan="5">Dampak</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Sangat Ringan (1)</th>
                                    <th class="text-center">Ringan (2)</th>
                                    <th class="text-center">Sedang (3)</th>
                                    <th class="text-center">Berat (4)</th>
                                    <th class="text-center">Sangat Berat (5)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center fw-bold">Sangat Sering (5)</td>
                                    <td class="text-center bg-warning">5</td>
                                    <td class="text-center bg-danger">10</td>
                                    <td class="text-center bg-danger">15</td>
                                    <td class="text-center bg-danger">20</td>
                                    <td class="text-center bg-danger">25</td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-bold">Sering (4)</td>
                                    <td class="text-center bg-warning">4</td>
                                    <td class="text-center bg-warning">8</td>
                                    <td class="text-center bg-danger">12</td>
                                    <td class="text-center bg-danger">16</td>
                                    <td class="text-center bg-danger">20</td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-bold">Mungkin (3)</td>
                                    <td class="text-center bg-success">3</td>
                                    <td class="text-center bg-warning">6</td>
                                    <td class="text-center bg-warning">9</td>
                                    <td class="text-center bg-danger">12</td>
                                    <td class="text-center bg-danger">15</td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-bold">Jarang (2)</td>
                                    <td class="text-center bg-success">2</td>
                                    <td class="text-center bg-success">4</td>
                                    <td class="text-center bg-warning">6</td>
                                    <td class="text-center bg-warning">8</td>
                                    <td class="text-center bg-danger">10</td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-bold">Sangat Jarang (1)</td>
                                    <td class="text-center bg-success">1</td>
                                    <td class="text-center bg-success">2</td>
                                    <td class="text-center bg-success">3</td>
                                    <td class="text-center bg-warning">4</td>
                                    <td class="text-center bg-warning">5</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Keterangan Level Risiko:</h5>
                            <div class="d-flex flex-wrap gap-3 mt-2">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success p-2 me-2" style="width:30px; height:30px;"></div>
                                    <span>Rendah (1-4)</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning p-2 me-2" style="width:30px; height:30px;"></div>
                                    <span>Sedang (5-9)</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger p-2 me-2" style="width:30px; height:30px;"></div>
                                    <span>Tinggi (10-25)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Panduan Penggunaan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="fw-bold">Penilaian Probabilitas</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Sangat Jarang (1)
                                <span class="badge bg-secondary">1 kali / tahun</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Jarang (2)
                                <span class="badge bg-secondary">1-2 kali / 6 bulan</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Mungkin (3)
                                <span class="badge bg-secondary">1-2 kali / bulan</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Sering (4)
                                <span class="badge bg-secondary">1-2 kali / minggu</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Sangat Sering (5)
                                <span class="badge bg-secondary">Hampir setiap hari</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h6 class="fw-bold">Penilaian Dampak</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Sangat Ringan (1)
                                <span class="badge bg-success">Minimal</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Ringan (2)
                                <span class="badge bg-success">Minor</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Sedang (3)
                                <span class="badge bg-warning">Moderat</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Berat (4)
                                <span class="badge bg-danger">Mayor</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Sangat Berat (5)
                                <span class="badge bg-danger">Katastropik</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Tindakan Pengendalian</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="d-flex align-items-center">
                            <span class="badge bg-success me-2">Rendah</span>
                            <span>Tindakan yang disarankan:</span>
                        </h6>
                        <p class="small text-muted">Penanganan secara rutin, prosedur standar, dapat diterima, dicatat dalam formulir insiden.</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="d-flex align-items-center">
                            <span class="badge bg-warning me-2">Sedang</span>
                            <span>Tindakan yang disarankan:</span>
                        </h6>
                        <p class="small text-muted">Perlu perhatian oleh manajemen unit, penanganan dengan rencana tindakan tertentu.</p>
                    </div>
                    
                    <div>
                        <h6 class="d-flex align-items-center">
                            <span class="badge bg-danger me-2">Tinggi</span>
                            <span>Tindakan yang disarankan:</span>
                        </h6>
                        <p class="small text-muted">Perlu perhatian serius dan segera dari manajemen senior, diperlukan rencana tindakan terperinci dan monitoring mendalam.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table td, .table th {
        vertical-align: middle;
    }
    .bg-success {
        background-color: #28a745 !important;
        color: white;
    }
    .bg-warning {
        background-color: #ffc107 !important;
        color: black;
    }
    .bg-danger {
        background-color: #dc3545 !important;
        color: white;
    }
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