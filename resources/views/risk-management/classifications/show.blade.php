@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detail Klasifikasi Risiko</h4>
                    <div class="d-flex">
                        <a href="{{ route('risk-management.incidents.show', $classification->incident_id) }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Kembali ke Insiden
                        </a>
                        @can('update', $classification)
                        <a href="{{ route('risk-management.classifications.edit', $classification->id) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        @endcan
                        @can('delete', $classification)
                        <form action="{{ route('risk-management.classifications.destroy', $classification->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus klasifikasi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Hapus</button>
                        </form>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Detail Insiden</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">ID Insiden</th>
                                    <td>{{ $classification->incident->id }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Kejadian</th>
                                    <td>{{ $classification->incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Lokasi</th>
                                    <td>{{ $classification->incident->location->name }}</td>
                                </tr>
                                <tr>
                                    <th>Jenis Insiden</th>
                                    <td>{{ $classification->incident->incidentType->name }}</td>
                                </tr>
                                <tr>
                                    <th>Kronologis</th>
                                    <td>{{ $classification->incident->kronologis }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Hasil Penilaian Risiko</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Dampak</th>
                                    <td>{{ $classification->dampak }} - 
                                        @switch($classification->dampak)
                                            @case(1)
                                                Minimal (Tidak ada cedera)
                                                @break
                                            @case(2)
                                                Minor (Cedera ringan)
                                                @break
                                            @case(3)
                                                Moderate (Cedera sedang)
                                                @break
                                            @case(4)
                                                Major (Cedera berat)
                                                @break
                                            @case(5)
                                                Extreme (Kematian/cacat permanen)
                                                @break
                                            @default
                                                Tidak didefinisikan
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>Detail Dampak</th>
                                    <td>{{ $classification->dampak_detail }}</td>
                                </tr>
                                <tr>
                                    <th>Probabilitas</th>
                                    <td>{{ $classification->probabilitas }} - 
                                        @switch($classification->probabilitas)
                                            @case(1)
                                                Sangat Jarang (>5 tahun sekali)
                                                @break
                                            @case(2)
                                                Jarang (1-5 tahun sekali)
                                                @break
                                            @case(3)
                                                Mungkin (Setahun sekali)
                                                @break
                                            @case(4)
                                                Sering (Sebulan sekali)
                                                @break
                                            @case(5)
                                                Sangat Sering (Seminggu sekali)
                                                @break
                                            @default
                                                Tidak didefinisikan
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>Detail Probabilitas</th>
                                    <td>{{ $classification->probabilitas_detail }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card mb-3 
                                                @if($classification->zona_risiko == 'Merah')
                                                    bg-danger
                                                @elseif($classification->zona_risiko == 'Kuning')
                                                    bg-warning
                                                @elseif($classification->zona_risiko == 'Hijau')
                                                    bg-success
                                                @else
                                                    bg-info
                                                @endif
                                                text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">Skor Risiko</h5>
                                                    <h1 class="display-4">{{ $classification->skor_risiko }}</h1>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card mb-3 
                                                @if($classification->zona_risiko == 'Merah')
                                                    bg-danger
                                                @elseif($classification->zona_risiko == 'Kuning')
                                                    bg-warning
                                                @elseif($classification->zona_risiko == 'Hijau')
                                                    bg-success
                                                @else
                                                    bg-info
                                                @endif
                                                text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">Level Risiko</h5>
                                                    <h2>{{ $classification->level_risiko }}</h2>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card mb-3 
                                                @if($classification->zona_risiko == 'Merah')
                                                    bg-danger
                                                @elseif($classification->zona_risiko == 'Kuning')
                                                    bg-warning
                                                @elseif($classification->zona_risiko == 'Hijau')
                                                    bg-success
                                                @else
                                                    bg-info
                                                @endif
                                                text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">Zona Risiko</h5>
                                                    <h2>{{ $classification->zona_risiko }}</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Informasi Klasifikasi</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Diklasifikasi Oleh</th>
                                    <td>{{ $classification->classifier->name }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Klasifikasi</th>
                                    <td>{{ $classification->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Diupdate</th>
                                    <td>{{ $classification->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 