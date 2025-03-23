@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Klasifikasi & Skoring Risiko</h4>
                    <span class="badge bg-primary">ID Insiden: {{ $incident->id }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Detail Insiden</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="30%">Tanggal Kejadian</th>
                                    <td>{{ $incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Lokasi</th>
                                    <td>{{ $incident->location->name }}</td>
                                </tr>
                                <tr>
                                    <th>Jenis Insiden</th>
                                    <td>{{ $incident->incidentType->name }}</td>
                                </tr>
                                <tr>
                                    <th>Kronologis</th>
                                    <td>{{ $incident->kronologis }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Matriks Risiko</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered risk-matrix">
                                            <thead>
                                                <tr>
                                                    <th colspan="2" rowspan="2" class="text-center align-middle">Risk Matrix</th>
                                                    <th colspan="5" class="text-center">Dampak</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-center">1<br>Minimal</th>
                                                    <th class="text-center">2<br>Minor</th>
                                                    <th class="text-center">3<br>Moderate</th>
                                                    <th class="text-center">4<br>Major</th>
                                                    <th class="text-center">5<br>Extreme</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th rowspan="5" class="align-middle text-center" style="writing-mode: vertical-rl; transform: rotate(180deg);">Probabilitas</th>
                                                    <th class="text-center">5<br>Sangat Sering</th>
                                                    <td class="bg-warning text-center">5</td>
                                                    <td class="bg-warning text-center">10</td>
                                                    <td class="bg-danger text-center">15</td>
                                                    <td class="bg-danger text-center">20</td>
                                                    <td class="bg-danger text-center">25</td>
                                                </tr>
                                                <tr>
                                                    <th class="text-center">4<br>Sering</th>
                                                    <td class="bg-warning text-center">4</td>
                                                    <td class="bg-warning text-center">8</td>
                                                    <td class="bg-danger text-center">12</td>
                                                    <td class="bg-danger text-center">16</td>
                                                    <td class="bg-danger text-center">20</td>
                                                </tr>
                                                <tr>
                                                    <th class="text-center">3<br>Mungkin</th>
                                                    <td class="bg-success text-center">3</td>
                                                    <td class="bg-warning text-center">6</td>
                                                    <td class="bg-warning text-center">9</td>
                                                    <td class="bg-danger text-center">12</td>
                                                    <td class="bg-danger text-center">15</td>
                                                </tr>
                                                <tr>
                                                    <th class="text-center">2<br>Jarang</th>
                                                    <td class="bg-success text-center">2</td>
                                                    <td class="bg-success text-center">4</td>
                                                    <td class="bg-warning text-center">6</td>
                                                    <td class="bg-warning text-center">8</td>
                                                    <td class="bg-danger text-center">10</td>
                                                </tr>
                                                <tr>
                                                    <th class="text-center">1<br>Sangat Jarang</th>
                                                    <td class="bg-info text-center">1</td>
                                                    <td class="bg-success text-center">2</td>
                                                    <td class="bg-success text-center">3</td>
                                                    <td class="bg-warning text-center">4</td>
                                                    <td class="bg-warning text-center">5</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="badge bg-info p-2">Risiko Rendah (1)</span>
                                            <span class="badge bg-success p-2">Risiko Rendah (2-3)</span>
                                            <span class="badge bg-warning p-2">Risiko Sedang (4-10)</span>
                                            <span class="badge bg-danger p-2">Risiko Tinggi (>10)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('risk-management.classifications.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="incident_id" value="{{ $incident->id }}">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dampak">Dampak (1-5) <span class="text-danger">*</span></label>
                                    <select name="dampak" id="dampak" class="form-control @error('dampak') is-invalid @enderror" required>
                                        <option value="">-- Pilih Dampak --</option>
                                        <option value="1">1 - Minimal (Tidak ada cedera)</option>
                                        <option value="2">2 - Minor (Cedera ringan)</option>
                                        <option value="3">3 - Moderate (Cedera sedang)</option>
                                        <option value="4">4 - Major (Cedera berat)</option>
                                        <option value="5">5 - Extreme (Kematian/cacat permanen)</option>
                                    </select>
                                    @error('dampak')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mt-3">
                                    <label for="dampak_detail">Penjelasan Dampak</label>
                                    <textarea name="dampak_detail" id="dampak_detail" rows="3" class="form-control @error('dampak_detail') is-invalid @enderror">{{ old('dampak_detail') }}</textarea>
                                    @error('dampak_detail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="probabilitas">Probabilitas (1-5) <span class="text-danger">*</span></label>
                                    <select name="probabilitas" id="probabilitas" class="form-control @error('probabilitas') is-invalid @enderror" required>
                                        <option value="">-- Pilih Probabilitas --</option>
                                        <option value="1">1 - Sangat Jarang (>5 tahun sekali)</option>
                                        <option value="2">2 - Jarang (1-5 tahun sekali)</option>
                                        <option value="3">3 - Mungkin (Setahun sekali)</option>
                                        <option value="4">4 - Sering (Sebulan sekali)</option>
                                        <option value="5">5 - Sangat Sering (Seminggu sekali)</option>
                                    </select>
                                    @error('probabilitas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mt-3">
                                    <label for="probabilitas_detail">Penjelasan Probabilitas</label>
                                    <textarea name="probabilitas_detail" id="probabilitas_detail" rows="3" class="form-control @error('probabilitas_detail') is-invalid @enderror">{{ old('probabilitas_detail') }}</textarea>
                                    @error('probabilitas_detail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Hasil Penilaian Risiko</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Skor Risiko</label>
                                                    <input type="text" id="skor_risiko" name="skor_risiko" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Level Risiko</label>
                                                    <input type="text" id="level_risiko" name="level_risiko" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Zona Risiko</label>
                                                    <input type="text" id="zona_risiko" name="zona_risiko" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('risk-management.incidents.show', $incident->id) }}" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Klasifikasi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Fungsi untuk menghitung skor risiko dan level
        function calculateRisk() {
            var dampak = parseInt($('#dampak').val()) || 0;
            var probabilitas = parseInt($('#probabilitas').val()) || 0;
            
            if (dampak > 0 && probabilitas > 0) {
                var skor = dampak * probabilitas;
                $('#skor_risiko').val(skor);
                
                // Tentukan level risiko berdasarkan skor
                var level, zona, warna;
                
                if (skor == 1) {
                    level = 'Risiko Rendah';
                    zona = 'Biru';
                    warna = 'info';
                } else if (skor >= 2 && skor <= 3) {
                    level = 'Risiko Rendah';
                    zona = 'Hijau';
                    warna = 'success';
                } else if (skor >= 4 && skor <= 10) {
                    level = 'Risiko Sedang';
                    zona = 'Kuning';
                    warna = 'warning';
                } else {
                    level = 'Risiko Tinggi';
                    zona = 'Merah';
                    warna = 'danger';
                }
                
                $('#level_risiko').val(level);
                $('#zona_risiko').val(zona);
                
                // Ubah warna latar belakang sesuai zona
                $('#skor_risiko').removeClass('bg-info bg-success bg-warning bg-danger');
                $('#level_risiko').removeClass('bg-info bg-success bg-warning bg-danger');
                $('#zona_risiko').removeClass('bg-info bg-success bg-warning bg-danger');
                
                $('#skor_risiko').addClass('bg-' + warna + ' text-white');
                $('#level_risiko').addClass('bg-' + warna + ' text-white');
                $('#zona_risiko').addClass('bg-' + warna + ' text-white');
            } else {
                $('#skor_risiko').val('');
                $('#level_risiko').val('');
                $('#zona_risiko').val('');
                
                $('#skor_risiko').removeClass('bg-info bg-success bg-warning bg-danger text-white');
                $('#level_risiko').removeClass('bg-info bg-success bg-warning bg-danger text-white');
                $('#zona_risiko').removeClass('bg-info bg-success bg-warning bg-danger text-white');
            }
        }
        
        // Panggil fungsi saat nilai dampak atau probabilitas berubah
        $('#dampak, #probabilitas').change(function() {
            calculateRisk();
        });
    });
</script>
@endpush
@endsection 