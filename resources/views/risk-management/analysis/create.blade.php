@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Analisis Akar Masalah</h4>
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
                            <h5 class="border-bottom pb-2">Hasil Klasifikasi Risiko</h5>
                            @if($incident->classification)
                                <table class="table table-sm">
                                    <tr>
                                        <th width="30%">Dampak</th>
                                        <td>{{ $incident->classification->dampak }} - {{ $incident->classification->dampak_detail }}</td>
                                    </tr>
                                    <tr>
                                        <th>Probabilitas</th>
                                        <td>{{ $incident->classification->probabilitas }} - {{ $incident->classification->probabilitas_detail }}</td>
                                    </tr>
                                    <tr>
                                        <th>Skor Risiko</th>
                                        <td>{{ $incident->classification->skor_risiko }}</td>
                                    </tr>
                                    <tr>
                                        <th>Level Risiko</th>
                                        <td>
                                            <span class="badge bg-{{ $incident->classification->zona_risiko == 'Merah' ? 'danger' : ($incident->classification->zona_risiko == 'Kuning' ? 'warning' : ($incident->classification->zona_risiko == 'Hijau' ? 'success' : 'info')) }}">
                                                {{ $incident->classification->level_risiko }} ({{ $incident->classification->zona_risiko }})
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            @else
                                <div class="alert alert-warning">
                                    Insiden ini belum diklasifikasikan. <a href="{{ route('risk-management.classifications.create', ['incident_id' => $incident->id]) }}" class="alert-link">Klasifikasikan sekarang</a>.
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <form action="{{ route('risk-management.analysis.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="incident_id" value="{{ $incident->id }}">
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="root_causes">Penyebab Langsung / Akar Masalah <span class="text-danger">*</span></label>
                                    <textarea name="root_causes" id="root_causes" rows="3" class="form-control @error('root_causes') is-invalid @enderror" required>{{ old('root_causes') }}</textarea>
                                    @error('root_causes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5>Faktor Kontributor</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <ul class="nav nav-tabs" id="factorTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="tim-tab" data-bs-toggle="tab" data-bs-target="#tim" type="button" role="tab" aria-controls="tim" aria-selected="true">Faktor Tim</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="sistem-tab" data-bs-toggle="tab" data-bs-target="#sistem" type="button" role="tab" aria-controls="sistem" aria-selected="false">Faktor Sistem</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pasien-tab" data-bs-toggle="tab" data-bs-target="#pasien" type="button" role="tab" aria-controls="pasien" aria-selected="false">Faktor Pasien</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="lingkungan-tab" data-bs-toggle="tab" data-bs-target="#lingkungan" type="button" role="tab" aria-controls="lingkungan" aria-selected="false">Faktor Lingkungan</button>
                                            </li>
                                        </ul>
                                        <div class="tab-content pt-3" id="factorTabsContent">
                                            <div class="tab-pane fade show active" id="tim" role="tabpanel" aria-labelledby="tim-tab">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_tim[]" id="tim_komunikasi" value="Komunikasi tidak efektif">
                                                            <label class="form-check-label" for="tim_komunikasi">Komunikasi tidak efektif</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_tim[]" id="tim_supervisi" value="Supervisi kurang">
                                                            <label class="form-check-label" for="tim_supervisi">Supervisi kurang</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_tim[]" id="tim_teamwork" value="Kerjasama tim kurang">
                                                            <label class="form-check-label" for="tim_teamwork">Kerjasama tim kurang</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_tim[]" id="tim_sop" value="Tidak mengikuti SOP">
                                                            <label class="form-check-label" for="tim_sop">Tidak mengikuti SOP</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_tim[]" id="tim_kompetensi" value="Kompetensi kurang">
                                                            <label class="form-check-label" for="tim_kompetensi">Kompetensi kurang</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_tim[]" id="tim_kelelahan" value="Kelelahan/stress">
                                                            <label class="form-check-label" for="tim_kelelahan">Kelelahan/stress</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <div class="form-group">
                                                            <label for="team_factors">Faktor Tim Lainnya</label>
                                                            <textarea name="team_factors" id="team_factors" rows="2" class="form-control">{{ old('team_factors') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="tab-pane fade" id="sistem" role="tabpanel" aria-labelledby="sistem-tab">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_sistem[]" id="sistem_policy" value="Kebijakan/prosedur kurang jelas">
                                                            <label class="form-check-label" for="sistem_policy">Kebijakan/prosedur kurang jelas</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_sistem[]" id="sistem_komunikasi" value="Sistem komunikasi tidak memadai">
                                                            <label class="form-check-label" for="sistem_komunikasi">Sistem komunikasi tidak memadai</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_sistem[]" id="sistem_alat" value="Peralatan/teknologi kurang memadai">
                                                            <label class="form-check-label" for="sistem_alat">Peralatan/teknologi kurang memadai</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_sistem[]" id="sistem_resources" value="Sumber daya manusia kurang">
                                                            <label class="form-check-label" for="sistem_resources">Sumber daya manusia kurang</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_sistem[]" id="sistem_training" value="Pelatihan kurang">
                                                            <label class="form-check-label" for="sistem_training">Pelatihan kurang</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_sistem[]" id="sistem_dokumentasi" value="Dokumentasi kurang baik">
                                                            <label class="form-check-label" for="sistem_dokumentasi">Dokumentasi kurang baik</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <div class="form-group">
                                                            <label for="system_factors">Faktor Sistem Lainnya</label>
                                                            <textarea name="system_factors" id="system_factors" rows="2" class="form-control">{{ old('system_factors') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="tab-pane fade" id="pasien" role="tabpanel" aria-labelledby="pasien-tab">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_pasien[]" id="pasien_kondisi" value="Kondisi klinis yang kompleks">
                                                            <label class="form-check-label" for="pasien_kondisi">Kondisi klinis yang kompleks</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_pasien[]" id="pasien_perilaku" value="Perilaku pasien tidak kooperatif">
                                                            <label class="form-check-label" for="pasien_perilaku">Perilaku pasien tidak kooperatif</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_pasien[]" id="pasien_komunikasi" value="Kesulitan komunikasi dengan pasien">
                                                            <label class="form-check-label" for="pasien_komunikasi">Kesulitan komunikasi dengan pasien</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_pasien[]" id="pasien_keluarga" value="Keterlibatan keluarga kurang">
                                                            <label class="form-check-label" for="pasien_keluarga">Keterlibatan keluarga kurang</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_pasien[]" id="pasien_kognitif" value="Gangguan kognitif pada pasien">
                                                            <label class="form-check-label" for="pasien_kognitif">Gangguan kognitif pada pasien</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_pasien[]" id="pasien_pendidikan" value="Faktor pendidikan/pengetahuan pasien">
                                                            <label class="form-check-label" for="pasien_pendidikan">Faktor pendidikan/pengetahuan pasien</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <div class="form-group">
                                                            <label for="patient_factors">Faktor Pasien Lainnya</label>
                                                            <textarea name="patient_factors" id="patient_factors" rows="2" class="form-control">{{ old('patient_factors') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="tab-pane fade" id="lingkungan" role="tabpanel" aria-labelledby="lingkungan-tab">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_lingkungan[]" id="lingkungan_kebisingan" value="Kebisingan">
                                                            <label class="form-check-label" for="lingkungan_kebisingan">Kebisingan</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_lingkungan[]" id="lingkungan_pencahayaan" value="Pencahayaan kurang">
                                                            <label class="form-check-label" for="lingkungan_pencahayaan">Pencahayaan kurang</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_lingkungan[]" id="lingkungan_suhu" value="Suhu ruangan tidak sesuai">
                                                            <label class="form-check-label" for="lingkungan_suhu">Suhu ruangan tidak sesuai</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_lingkungan[]" id="lingkungan_desain" value="Desain ruangan tidak sesuai">
                                                            <label class="form-check-label" for="lingkungan_desain">Desain ruangan tidak sesuai</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_lingkungan[]" id="lingkungan_kepadatan" value="Kepadatan pasien">
                                                            <label class="form-check-label" for="lingkungan_kepadatan">Kepadatan pasien</label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="faktor_lingkungan[]" id="lingkungan_kebersihan" value="Kebersihan kurang">
                                                            <label class="form-check-label" for="lingkungan_kebersihan">Kebersihan kurang</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <div class="form-group">
                                                            <label for="environmental_factors">Faktor Lingkungan Lainnya</label>
                                                            <textarea name="environmental_factors" id="environmental_factors" rows="2" class="form-control">{{ old('environmental_factors') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="analysis_method">Teknik Analisis yang Digunakan <span class="text-danger">*</span></label>
                                    <select name="analysis_method" id="analysis_method" class="form-control @error('analysis_method') is-invalid @enderror" required>
                                        <option value="">-- Pilih Teknik Analisis --</option>
                                        <option value="RCA" {{ old('analysis_method') == 'RCA' ? 'selected' : '' }}>Root Cause Analysis (RCA)</option>
                                        <option value="Fishbone" {{ old('analysis_method') == 'Fishbone' ? 'selected' : '' }}>Diagram Fishbone</option>
                                        <option value="5Why" {{ old('analysis_method') == '5Why' ? 'selected' : '' }}>5 Why</option>
                                        <option value="Lainnya" {{ old('analysis_method') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('analysis_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div id="teknik_lainnya_container" class="form-group mt-2" style="display: none;">
                                    <label for="teknik_lainnya">Sebutkan Teknik Lainnya</label>
                                    <input type="text" name="teknik_lainnya" id="teknik_lainnya" class="form-control" value="{{ old('teknik_lainnya') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5>Rekomendasi Perbaikan</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="recommendations">Rekomendasi Tindakan <span class="text-danger">*</span></label>
                                            <textarea name="recommendations" id="recommendations" rows="5" class="form-control @error('recommendations') is-invalid @enderror" required>{{ old('recommendations') }}</textarea>
                                            @error('recommendations')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('risk-management.incidents.show', $incident->id) }}" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Analisis</button>
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
        // Tampilkan field teknik lainnya jika opsi "Lainnya" dipilih
        $('#analysis_method').change(function() {
            if ($(this).val() == 'Lainnya') {
                $('#teknik_lainnya_container').show();
            } else {
                $('#teknik_lainnya_container').hide();
            }
        });
        
        // Cek saat halaman load pertama kali
        if ($('#analysis_method').val() == 'Lainnya') {
            $('#teknik_lainnya_container').show();
        }

        // Trigger change pada load halaman
        $('#analysis_method').trigger('change');
    });
</script>
@endpush
@endsection 