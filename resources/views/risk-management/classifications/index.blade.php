@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Klasifikasi Risiko</h4>
                    <div>
                        <a href="{{ route('risk-management.incidents.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Insiden
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="classifications-table">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>ID</th>
                                    <th>Insiden</th>
                                    <th>Tanggal Insiden</th>
                                    <th>Skor Risiko</th>
                                    <th>Level Risiko</th>
                                    <th>Zona Risiko</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Tanggal Klasifikasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classifications as $classification)
                                    <tr>
                                        <td>{{ $classification->id }}</td>
                                        <td>
                                            <a href="{{ route('risk-management.incidents.show', $classification->incident_id) }}">
                                                {{ $classification->incident->incidentType->code }}-{{ $classification->incident_id }}
                                            </a>
                                        </td>
                                        <td>{{ $classification->incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $classification->zona_risiko == 'Merah' ? 'danger' : 
                                                ($classification->zona_risiko == 'Kuning' ? 'warning' : 
                                                ($classification->zona_risiko == 'Hijau' ? 'success' : 'info')) }} p-2">
                                                {{ $classification->skor_risiko }}
                                            </span>
                                        </td>
                                        <td>{{ $classification->level_risiko }}</td>
                                        <td>
                                            <span class="badge bg-{{ $classification->zona_risiko == 'Merah' ? 'danger' : 
                                                ($classification->zona_risiko == 'Kuning' ? 'warning' : 
                                                ($classification->zona_risiko == 'Hijau' ? 'success' : 'info')) }}">
                                                {{ $classification->zona_risiko }}
                                            </span>
                                        </td>
                                        <td>{{ $classification->classifier->name }}</td>
                                        <td>{{ $classification->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('risk-management.incidents.show', $classification->incident_id) }}" class="btn btn-sm btn-info" title="Lihat Insiden">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('update', $classification)
                                                    <a href="{{ route('risk-management.classifications.edit', $classification->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete', $classification)
                                                    <form action="{{ route('risk-management.classifications.destroy', $classification->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus klasifikasi ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Belum ada data klasifikasi risiko.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#classifications-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "order": [[ 0, "desc" ]]
        });
    });
</script>
@endpush
@endsection 