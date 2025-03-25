@extends('layouts.app')

@section('title', 'Undangan Rapat')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Undangan Rapat</h1>
        <a href="{{ route('document-management.invitations.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Buat Undangan
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Undangan Rapat</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Filter:</div>
                            <a class="dropdown-item" href="{{ route('document-management.invitations.index') }}">Semua</a>
                            <a class="dropdown-item" href="{{ route('document-management.invitations.index', ['status' => 'draft']) }}">Draft</a>
                            <a class="dropdown-item" href="{{ route('document-management.invitations.index', ['status' => 'published']) }}">Dipublikasikan</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Dokumen</th>
                                    <th>Judul Rapat</th>
                                    <th>Waktu Rapat</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invitations as $index => $invitation)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $invitation->document_number }}</td>
                                    <td>{{ $invitation->invitation->meeting_title }}</td>
                                    <td>{{ $invitation->invitation->meeting_datetime->format('d-m-Y H:i') }}</td>
                                    <td>{{ $invitation->invitation->meeting_location }}</td>
                                    <td>
                                        @if($invitation->status == 'draft')
                                            <span class="badge badge-warning">Draft</span>
                                        @elseif($invitation->status == 'published')
                                            <span class="badge badge-success">Dipublikasikan</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $invitation->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('document-management.invitations.show', $invitation->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($invitation->status == 'draft')
                                            <a href="{{ route('document-management.invitations.edit', $invitation->id) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('document-management.invitations.destroy', $invitation->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus undangan ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $invitations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 