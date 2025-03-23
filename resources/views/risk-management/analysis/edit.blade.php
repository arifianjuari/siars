@extends('layouts.app')

@section('title', 'Edit Analisis Akar Masalah | Manajemen Risiko')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Analisis Akar Masalah</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('risk-management.dashboard') }}">Manajemen Risiko</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('risk-management.analysis.index') }}">Analisis Akar Masalah</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Form Edit Analisis Akar Masalah</h3>
                    </div>
                    <!-- /.card-header -->
                    
                    <!-- form start -->
                    <form method="POST" action="{{ route('risk-management.analysis.update', ['analysis' => $analysis->id]) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-check"></i> Sukses!</h5>
                                    {{ session('success') }}
                                </div>
                            @endif
    
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                    {{ session('error') }}
                                </div>
                            @endif
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-outline card-info">
                                        <div class="card-header">
                                            <h3 class="card-title">Informasi Insiden</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th style="width: 200px">ID Insiden</th>
                                                    <td>{{ $analysis->incident->id }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tanggal & Waktu Kejadian</th>
                                                    <td>{{ $analysis->incident->tanggal_waktu_kejadian->format('d/m/Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Lokasi</th>
                                                    <td>{{ $analysis->incident->location->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Jenis Insiden</th>
                                                    <td>{{ $analysis->incident->incidentType->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tingkat Risiko</th>
                                                    <td>
                                                        @if($analysis->incident->classification)
                                                            @php
                                                                $riskLevel = $analysis->incident->classification->risk_level;
                                                                $badgeClass = '';
                                                                switch($riskLevel) {
                                                                    case 'Rendah':
                                                                        $badgeClass = 'bg-success';
                                                                        break;
                                                                    case 'Sedang':
                                                                        $badgeClass = 'bg-warning';
                                                                        break;
                                                                    case 'Tinggi':
                                                                        $badgeClass = 'bg-danger';
                                                                        break;
                                                                    case 'Ekstrem':
                                                                        $badgeClass = 'bg-dark';
                                                                        break;
                                                                    default:
                                                                        $badgeClass = 'bg-info';
                                                                }
                                                            @endphp
                                                            <span class="badge {{ $badgeClass }}">
                                                                {{ $riskLevel }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">Belum diklasifikasi</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-outline card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Kronologis Insiden</h3>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-justify">{!! nl2br(e($analysis->incident->kronologis)) !!}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="analysis_method">Metode Analisis <span class="text-danger">*</span></label>
                                        <select name="analysis_method" id="analysis_method" class="form-control @error('analysis_method') is-invalid @enderror" required>
                                            <option value="">-- Pilih Metode Analisis --</option>
                                            <option value="RCA" {{ old('analysis_method', $analysis->analysis_method) == 'RCA' ? 'selected' : '' }}>Root Cause Analysis (RCA)</option>
                                            <option value="Fishbone" {{ old('analysis_method', $analysis->analysis_method) == 'Fishbone' ? 'selected' : '' }}>Diagram Fishbone</option>
                                            <option value="5Why" {{ old('analysis_method', $analysis->analysis_method) == '5Why' ? 'selected' : '' }}>5 Why</option>
                                            <option value="FMEA" {{ old('analysis_method', $analysis->analysis_method) == 'FMEA' ? 'selected' : '' }}>Failure Mode and Effects Analysis (FMEA)</option>
                                            <option value="Lainnya" {{ old('analysis_method', $analysis->analysis_method) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                        @error('analysis_method')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div id="teknik_lainnya_container" class="row mt-2" style="display: none;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="teknik_lainnya">Sebutkan Teknik Lainnya</label>
                                        <input type="text" name="teknik_lainnya" id="teknik_lainnya" class="form-control" value="{{ old('teknik_lainnya') }}">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="root_causes">Akar Masalah <span class="text-danger">*</span></label>
                                        <textarea name="root_causes" id="root_causes" rows="4" class="form-control @error('root_causes') is-invalid @enderror" placeholder="Tuliskan akar masalah yang teridentifikasi" required>{{ old('root_causes', $analysis->root_causes) }}</textarea>
                                        @error('root_causes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="font-weight-bold mt-3 mb-3">Faktor Kontributor</h5>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Pilih Faktor Kontributor</h3>
                                        </div>
                                        <div class="card-body">
                                            <ul class="nav nav-tabs" id="factorTabs" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link active" id="tim-tab" data-bs-toggle="tab" data-bs-target="#tim" type="button" role="tab" aria-controls="tim" aria-selected="true">Faktor Tim</a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="sistem-tab" data-bs-toggle="tab" data-bs-target="#sistem" type="button" role="tab" aria-controls="sistem" aria-selected="false">Faktor Sistem</a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="pasien-tab" data-bs-toggle="tab" data-bs-target="#pasien" type="button" role="tab" aria-controls="pasien" aria-selected="false">Faktor Pasien</a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="lingkungan-tab" data-bs-toggle="tab" data-bs-target="#lingkungan" type="button" role="tab" aria-controls="lingkungan" aria-selected="false">Faktor Lingkungan</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content pt-3" id="factorTabsContent">
                                                <div class="tab-pane fade show active" id="tim" role="tabpanel" aria-labelledby="tim-tab">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_tim[]" id="tim_komunikasi" value="Komunikasi tidak efektif" {{ strpos($analysis->team_factors, 'Komunikasi tidak efektif') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="tim_komunikasi">Komunikasi tidak efektif</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_tim[]" id="tim_supervisi" value="Supervisi kurang" {{ strpos($analysis->team_factors, 'Supervisi kurang') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="tim_supervisi">Supervisi kurang</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_tim[]" id="tim_teamwork" value="Kerjasama tim kurang" {{ strpos($analysis->team_factors, 'Kerjasama tim kurang') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="tim_teamwork">Kerjasama tim kurang</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_tim[]" id="tim_sop" value="Tidak mengikuti SOP" {{ strpos($analysis->team_factors, 'Tidak mengikuti SOP') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="tim_sop">Tidak mengikuti SOP</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_tim[]" id="tim_kompetensi" value="Kompetensi kurang" {{ strpos($analysis->team_factors, 'Kompetensi kurang') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="tim_kompetensi">Kompetensi kurang</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_tim[]" id="tim_kelelahan" value="Kelelahan/stress" {{ strpos($analysis->team_factors, 'Kelelahan/stress') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="tim_kelelahan">Kelelahan/stress</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 mt-2">
                                                            <div class="form-group">
                                                                <label for="team_factors">Faktor Tim Lainnya</label>
                                                                <textarea name="team_factors" id="team_factors" rows="2" class="form-control">{{ preg_replace('/^.*Lainnya:\s*/s', '', $analysis->team_factors) }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="tab-pane fade" id="sistem" role="tabpanel" aria-labelledby="sistem-tab">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_sistem[]" id="sistem_policy" value="Kebijakan/prosedur kurang jelas" {{ strpos($analysis->system_factors, 'Kebijakan/prosedur kurang jelas') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="sistem_policy">Kebijakan/prosedur kurang jelas</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_sistem[]" id="sistem_komunikasi" value="Sistem komunikasi tidak memadai" {{ strpos($analysis->system_factors, 'Sistem komunikasi tidak memadai') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="sistem_komunikasi">Sistem komunikasi tidak memadai</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_sistem[]" id="sistem_alat" value="Peralatan/teknologi kurang memadai" {{ strpos($analysis->system_factors, 'Peralatan/teknologi kurang memadai') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="sistem_alat">Peralatan/teknologi kurang memadai</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_sistem[]" id="sistem_resources" value="Sumber daya manusia kurang" {{ strpos($analysis->system_factors, 'Sumber daya manusia kurang') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="sistem_resources">Sumber daya manusia kurang</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_sistem[]" id="sistem_training" value="Pelatihan kurang" {{ strpos($analysis->system_factors, 'Pelatihan kurang') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="sistem_training">Pelatihan kurang</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_sistem[]" id="sistem_dokumentasi" value="Dokumentasi kurang baik" {{ strpos($analysis->system_factors, 'Dokumentasi kurang baik') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="sistem_dokumentasi">Dokumentasi kurang baik</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 mt-2">
                                                            <div class="form-group">
                                                                <label for="system_factors">Faktor Sistem Lainnya</label>
                                                                <textarea name="system_factors" id="system_factors" rows="2" class="form-control">{{ preg_replace('/^.*Lainnya:\s*/s', '', $analysis->system_factors) }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="tab-pane fade" id="pasien" role="tabpanel" aria-labelledby="pasien-tab">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_pasien[]" id="pasien_kondisi" value="Kondisi klinis yang kompleks" {{ strpos($analysis->patient_factors, 'Kondisi klinis yang kompleks') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="pasien_kondisi">Kondisi klinis yang kompleks</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_pasien[]" id="pasien_perilaku" value="Perilaku pasien tidak kooperatif" {{ strpos($analysis->patient_factors, 'Perilaku pasien tidak kooperatif') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="pasien_perilaku">Perilaku pasien tidak kooperatif</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_pasien[]" id="pasien_komunikasi" value="Kesulitan komunikasi dengan pasien" {{ strpos($analysis->patient_factors, 'Kesulitan komunikasi dengan pasien') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="pasien_komunikasi">Kesulitan komunikasi dengan pasien</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_pasien[]" id="pasien_keluarga" value="Keterlibatan keluarga kurang" {{ strpos($analysis->patient_factors, 'Keterlibatan keluarga kurang') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="pasien_keluarga">Keterlibatan keluarga kurang</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_pasien[]" id="pasien_kognitif" value="Gangguan kognitif pada pasien" {{ strpos($analysis->patient_factors, 'Gangguan kognitif pada pasien') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="pasien_kognitif">Gangguan kognitif pada pasien</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_pasien[]" id="pasien_pendidikan" value="Faktor pendidikan/pengetahuan pasien" {{ strpos($analysis->patient_factors, 'Faktor pendidikan/pengetahuan pasien') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="pasien_pendidikan">Faktor pendidikan/pengetahuan pasien</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 mt-2">
                                                            <div class="form-group">
                                                                <label for="patient_factors">Faktor Pasien Lainnya</label>
                                                                <textarea name="patient_factors" id="patient_factors" rows="2" class="form-control">{{ preg_replace('/^.*Lainnya:\s*/s', '', $analysis->patient_factors) }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="tab-pane fade" id="lingkungan" role="tabpanel" aria-labelledby="lingkungan-tab">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_lingkungan[]" id="lingkungan_kebisingan" value="Kebisingan" {{ strpos($analysis->environmental_factors, 'Kebisingan') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="lingkungan_kebisingan">Kebisingan</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_lingkungan[]" id="lingkungan_pencahayaan" value="Pencahayaan kurang" {{ strpos($analysis->environmental_factors, 'Pencahayaan kurang') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="lingkungan_pencahayaan">Pencahayaan kurang</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_lingkungan[]" id="lingkungan_suhu" value="Suhu ruangan tidak sesuai" {{ strpos($analysis->environmental_factors, 'Suhu ruangan tidak sesuai') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="lingkungan_suhu">Suhu ruangan tidak sesuai</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_lingkungan[]" id="lingkungan_desain" value="Desain ruangan tidak sesuai" {{ strpos($analysis->environmental_factors, 'Desain ruangan tidak sesuai') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="lingkungan_desain">Desain ruangan tidak sesuai</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_lingkungan[]" id="lingkungan_kepadatan" value="Kepadatan pasien" {{ strpos($analysis->environmental_factors, 'Kepadatan pasien') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="lingkungan_kepadatan">Kepadatan pasien</label>
                                                            </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="faktor_lingkungan[]" id="lingkungan_kebersihan" value="Kebersihan kurang" {{ strpos($analysis->environmental_factors, 'Kebersihan kurang') !== false ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="lingkungan_kebersihan">Kebersihan kurang</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 mt-2">
                                                            <div class="form-group">
                                                                <label for="environmental_factors">Faktor Lingkungan Lainnya</label>
                                                                <textarea name="environmental_factors" id="environmental_factors" rows="2" class="form-control">{{ preg_replace('/^.*Lainnya:\s*/s', '', $analysis->environmental_factors) }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="recommendations">Rekomendasi Tindakan Perbaikan <span class="text-danger">*</span></label>
                                        <textarea name="recommendations" id="recommendations" rows="4" class="form-control @error('recommendations') is-invalid @enderror" placeholder="Tuliskan rekomendasi tindakan perbaikan" required>{{ old('recommendations', $analysis->recommendations) }}</textarea>
                                        @error('recommendations')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <a href="{{ route('risk-management.analysis.index') }}" class="btn btn-secondary">Batalkan</a>
                            <button type="submit" class="btn btn-primary float-right">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection

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

        // Bootstrap 5 mengaktifkan tab secara otomatis karena kita sudah menggunakan data-bs-toggle
        // Tidak perlu kode tambahan untuk inisialisasi tab
    });
</script>
@endpush
