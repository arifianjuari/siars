@extends('layouts.app')

@section('title', 'Validasi QR Code Gagal')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-left-danger mb-4">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">QR Code Tidak Valid</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-times-circle fa-4x text-danger"></i>
                        <h4 class="mt-3">Verifikasi Gagal</h4>
                        <p class="text-muted mt-2">{{ $message }}</p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        QR code yang dipindai tidak valid atau tidak ditemukan dalam sistem. 
                        Hal ini mungkin karena dokumen telah diubah atau QR code telah rusak.
                    </div>
                    
                    <div class="text-center mt-4">
                        @auth
                            <a href="{{ route('document-management.dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
                            </a>
                        @else
                            <p class="text-muted">Silakan hubungi administrator sistem untuk informasi lebih lanjut.</p>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 