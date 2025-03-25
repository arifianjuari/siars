@extends('layouts.app')

@section('title', 'Dashboard Manajemen Dokumen')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Manajemen Dokumen</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Total Dokumen Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Dokumen</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDocuments ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nota Dinas Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Nota Dinas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ ($notaDinasMasuk ?? 0) + ($notaDinasKeluar ?? 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Undangan Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Undangan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $undangan ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notulensi Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Notulensi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $notulensi ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Menu Cards -->
        <div class="col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Menu Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <a href="{{ route('document-management.memos.index', ['type' => 'nota_dinas_masuk']) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-inbox mr-2"></i> Nota Dinas Masuk
                            </a>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <a href="{{ route('document-management.memos.index', ['type' => 'nota_dinas_keluar']) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-paper-plane mr-2"></i> Nota Dinas Keluar
                            </a>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <a href="{{ route('document-management.invitations.index') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-calendar-alt mr-2"></i> Undangan Rapat
                            </a>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <a href="#" class="btn btn-primary btn-block">
                                <i class="fas fa-file-alt mr-2"></i> Notulensi Rapat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dokumen Terbaru -->
        <div class="col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dokumen Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @if(isset($recentDocuments) && count($recentDocuments) > 0)
                            @foreach($recentDocuments as $document)
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ $document->subject }}</h5>
                                        <small>{{ $document->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $document->document_number }}</p>
                                    <small>{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</small>
                                </a>
                            @endforeach
                        @else
                            <p class="text-center">Belum ada dokumen terbaru</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Selamat Datang di Modul Manajemen Dokumen</h6>
                </div>
                <div class="card-body">
                    <p>Modul Manajemen Dokumen digunakan untuk mengelola dokumen internal rumah sakit seperti nota dinas, undangan rapat, dan notulensi rapat.</p>
                    <p>Fitur utama modul ini meliputi:</p>
                    <ul>
                        <li>Pengelolaan Nota Dinas Masuk dan Keluar</li>
                        <li>Pengelolaan Undangan Rapat</li>
                        <li>Pengelolaan Notulensi Rapat</li>
                        <li>Riwayat dan tracking dokumen</li>
                        <li>Pencarian dan filter dokumen</li>
                        <li>Sistem QR code untuk "tanda tangan" digital dokumen</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 