@extends('layouts.app')

@section('title', 'Validasi QR Code')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-left-success mb-4">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">QR Code Valid</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle fa-4x text-success"></i>
                        <h4 class="mt-3">Dokumen Terverifikasi</h4>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Nomor Dokumen</th>
                                <td>{{ $document->document_number }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Dokumen</th>
                                <td>{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</td>
                            </tr>
                            <tr>
                                <th>Judul</th>
                                <td>{{ $document->subject }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Dokumen</th>
                                <td>{{ $document->document_date->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat Oleh</th>
                                <td>{{ $document->creator->name }}</td>
                            </tr>
                            <tr>
                                <th>Ditandatangani Oleh</th>
                                <td>{{ $signature->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Tanda Tangan</th>
                                <td>{{ $signature->signed_at->format('d-m-Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="text-center mt-4">
                        <p class="text-muted">Dokumen ini telah diverifikasi dan ditandatangani secara elektronik.</p>
                        @auth
                            <a href="{{ route('document-management.dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 