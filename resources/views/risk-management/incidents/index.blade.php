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

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Daftar Insiden & Histori</h4>
                    <a href="{{ route('risk-management.incidents.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Laporkan Insiden Baru
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <form action="{{ route('risk-management.incidents.index') }}" method="GET">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="date_range">Rentang Tanggal</label>
                                                    <div class="input-group">
                                                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                                                        <span class="input-group-text">s/d</span>
                                                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="lokasi_id">Lokasi</label>
                                                    <select name="lokasi_id" id="lokasi_id" class="form-control">
                                                        <option value="">Semua Lokasi</option>
                                                        @foreach($locations as $location)
                                                            <option value="{{ $location->id }}" {{ request('lokasi_id') == $location->id ? 'selected' : '' }}>
                                                                {{ $location->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="jenis_insiden_id">Jenis Insiden</label>
                                                    <select name="jenis_insiden_id" id="jenis_insiden_id" class="form-control">
                                                        <option value="">Semua Jenis</option>
                                                        @foreach($incidentTypes as $type)
                                                            <option value="{{ $type->id }}" {{ request('jenis_insiden_id') == $type->id ? 'selected' : '' }}>
                                                                {{ $type->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="level_risiko">Level Risiko</label>
                                                    <select name="level_risiko" id="level_risiko" class="form-control">
                                                        <option value="">Semua Level</option>
                                                        <option value="Rendah" {{ request('level_risiko') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                                                        <option value="Sedang" {{ request('level_risiko') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                                                        <option value="Tinggi" {{ request('level_risiko') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                                                        <option value="Ekstrim" {{ request('level_risiko') == 'Ekstrim' ? 'selected' : '' }}>Ekstrim</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="status">Status</label>
                                                    <select name="status" id="status" class="form-control">
                                                        <option value="">Semua Status</option>
                                                        <option value="Baru" {{ request('status') == 'Baru' ? 'selected' : '' }}>Baru</option>
                                                        <option value="Proses" {{ request('status') == 'Proses' ? 'selected' : '' }}>Dalam Proses</option>
                                                        <option value="Evaluasi" {{ request('status') == 'Evaluasi' ? 'selected' : '' }}>Evaluasi</option>
                                                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="keyword">Kata Kunci</label>
                                                    <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Nama pasien, No. RM, atau kata kunci lainnya" value="{{ request('keyword') }}">
                                                </div>
                                            </div>
                                            <div class="col-12 mt-3 d-flex justify-content-end">
                                                <button type="reset" class="btn btn-secondary me-2">Reset</button>
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Results -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tanggal Kejadian</th>
                                    <th>Lokasi</th>
                                    <th>No. RM</th>
                                    <th>Jenis Insiden</th>
                                    <th>Level Risiko</th>
                                    <th>Pelapor</th>
                                    <th>Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($incidents as $incident)
                                    <tr>
                                        <td>{{ $incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</td>
                                        <td>{{ $incident->location->name }}</td>
                                        <td>{{ $incident->no_rm }}</td>
                                        <td>{{ $incident->incidentType->name }}</td>
                                        <td>
                                            @if($incident->classification)
                                                <span class="badge bg-{{ $incident->classification->zona_risiko == 'Merah' ? 'danger' : ($incident->classification->zona_risiko == 'Kuning' ? 'warning' : 'success') }}">
                                                    {{ $incident->classification->level_risiko }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Belum diklasifikasi</span>
                                            @endif
                                        </td>
                                        <td>{{ $incident->reporter->name }}</td>
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
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('risk-management.incidents.show', $incident->id) }}" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('risk-management.incidents.edit', $incident->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if(!$incident->classification)
                                                    <a href="{{ route('risk-management.classifications.create', ['incident_id' => $incident->id]) }}" class="btn btn-sm btn-warning" title="Klasifikasi">
                                                        <i class="fas fa-clipboard-check"></i>
                                                    </a>
                                                @endif
                                                @if($incident->classification && !$incident->analysis)
                                                    <a href="{{ route('risk-management.analysis.create', ['incident_id' => $incident->id]) }}" class="btn btn-sm btn-primary" title="Analisis">
                                                        <i class="fas fa-search"></i>
                                                    </a>
                                                @endif
                                                @if($incident->analysis)
                                                    <a href="{{ route('risk-management.monitoring.edit', $incident->id) }}" class="btn btn-sm btn-secondary" title="Monitoring">
                                                        <i class="fas fa-tasks"></i>
                                                    </a>
                                                @endif
                                                @if($incident->status == 'Selesai')
                                                    <a href="{{ route('risk-management.reports.show', $incident->id) }}" class="btn btn-sm btn-success" title="Laporan">
                                                        <i class="fas fa-file-alt"></i>
                                                    </a>
                                                    <a href="{{ route('risk-management.incidents.export-pdf', $incident->id) }}" class="btn btn-sm btn-success" title="Unduh PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @endif
                                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $incident->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Modal Konfirmasi Hapus -->
                                            <div class="modal fade" id="deleteModal{{ $incident->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $incident->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $incident->id }}">Konfirmasi Hapus Insiden</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Apakah Anda yakin ingin menghapus insiden ini?</p>
                                                            <p><strong>Tanggal:</strong> {{ $incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</p>
                                                            <p><strong>Lokasi:</strong> {{ $incident->location->name }}</p>
                                                            <p><strong>No. RM:</strong> {{ $incident->no_rm }}</p>
                                                            <p class="text-danger">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait (klasifikasi, analisis, dan penanganan).</p>
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
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada data insiden yang ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $incidents->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Reset filter form
        $('button[type="reset"]').click(function() {
            $('#lokasi_id').val('');
            $('#jenis_insiden_id').val('');
            $('#level_risiko').val('');
            $('#status').val('');
            $('#keyword').val('');
            $('#start_date').val('');
            $('#end_date').val('');
        });
    });
</script>
@endpush
@endsection 