@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Formulir Pelaporan Insiden Keselamatan Pasien</h4>
                </div>
                <div class="card-body">
                    <style>
                        .loading-subtypes {
                            background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJmZWF0aGVyIGZlYXRoZXItbG9hZGVyIj48bGluZSB4MT0iMTIiIHkxPSIyIiB4Mj0iMTIiIHkyPSI2Ij48L2xpbmU+PGxpbmUgeDE9IjEyIiB5MT0iMTgiIHgyPSIxMiIgeTI9IjIyIj48L2xpbmU+PGxpbmUgeDE9IjQuOTMiIHkxPSI0LjkzIiB4Mj0iNy43NiIgeTI9IjcuNzYiPjwvbGluZT48bGluZSB4MT0iMTYuMjQiIHkxPSIxNi4yNCIgeDI9IjE5LjA3IiB5Mj0iMTkuMDciPjwvbGluZT48bGluZSB4MT0iMiIgeTE9IjEyIiB4Mj0iNiIgeTI9IjEyIj48L2xpbmU+PGxpbmUgeDE9IjE4IiB5MT0iMTIiIHgyPSIyMiIgeTI9IjEyIj48L2xpbmU+PGxpbmUgeDE9IjQuOTMiIHkxPSIxOS4wNyIgeDI9IjcuNzYiIHkyPSIxNi4yNCI+PC9saW5lPjxsaW5lIHgxPSIxNi4yNCIgeTE9IjcuNzYiIHgyPSIxOS4wNyIgeTI9IjQuOTMiPjwvbGluZT48L3N2Zz4=');
                            background-position: right 10px center;
                            background-repeat: no-repeat;
                            background-size: 20px;
                        }
                    </style>
                    <form action="{{ route('risk-management.incidents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_waktu_kejadian">Tanggal & Waktu Kejadian <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="tanggal_waktu_kejadian" id="tanggal_waktu_kejadian" class="form-control @error('tanggal_waktu_kejadian') is-invalid @enderror" required value="{{ old('tanggal_waktu_kejadian') }}">
                                    @error('tanggal_waktu_kejadian')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lokasi_id">Lokasi Kejadian <span class="text-danger">*</span></label>
                                    <select name="lokasi_id" id="lokasi_id" class="form-control @error('lokasi_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Lokasi --</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('lokasi_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lokasi_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_pasien">Nama Pasien / Inisial <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_pasien" id="nama_pasien" class="form-control @error('nama_pasien') is-invalid @enderror" required value="{{ old('nama_pasien') }}">
                                    @error('nama_pasien')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="no_rekam_medis">Nomor Rekam Medis <span class="text-danger">*</span></label>
                                    <input type="text" name="no_rekam_medis" id="no_rekam_medis" class="form-control @error('no_rekam_medis') is-invalid @enderror" required value="{{ old('no_rekam_medis') }}">
                                    @error('no_rekam_medis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_insiden_id">Jenis Insiden <span class="text-danger">*</span></label>
                                    <select name="jenis_insiden_id" id="jenis_insiden_id" class="form-control @error('jenis_insiden_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Jenis Insiden --</option>
                                        @foreach($incidentTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('jenis_insiden_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }} {{ $type->code ? '('.$type->code.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('jenis_insiden_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="incident_subtype_id">Subtipe Insiden <span class="text-danger">*</span></label>
                                    <select name="incident_subtype_id" id="incident_subtype_id" class="form-control @error('incident_subtype_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Subtipe Insiden --</option>
                                        @if(old('incident_subtype_id') && old('jenis_insiden_id'))
                                            <option value="{{ old('incident_subtype_id') }}" selected>Memuat...</option>
                                        @endif
                                    </select>
                                    @error('incident_subtype_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Jika dropdown tidak muncul, silakan <a href="{{ route('risk-management.incidents.get-subtypes') }}?incident_type_id=7" target="_blank">klik di sini untuk menguji endpoint</a>.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="kronologis">Kronologis Singkat <span class="text-danger">*</span></label>
                                    <textarea name="kronologis" id="kronologis" rows="4" class="form-control @error('kronologis') is-invalid @enderror" required>{{ old('kronologis') }}</textarea>
                                    @error('kronologis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="tindakan_langsung">Tindakan Langsung yang Dilakukan <span class="text-danger">*</span></label>
                                    <textarea name="tindakan_langsung" id="tindakan_langsung" rows="3" class="form-control @error('tindakan_langsung') is-invalid @enderror" required>{{ old('tindakan_langsung') }}</textarea>
                                    @error('tindakan_langsung')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nama_pelapor">Nama Pelapor <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_pelapor" id="nama_pelapor" class="form-control @error('nama_pelapor') is-invalid @enderror" required value="{{ old('nama_pelapor', Auth::user()->name) }}">
                                    @error('nama_pelapor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="profesi_id">Profesi <span class="text-danger">*</span></label>
                                    <select name="profesi_id" id="profesi_id" class="form-control @error('profesi_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Profesi --</option>
                                        @foreach($professions as $profession)
                                            <option value="{{ $profession->id }}" {{ old('profesi_id') == $profession->id ? 'selected' : '' }}>
                                                {{ $profession->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('profesi_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pernah Terjadi Sebelumnya? <span class="text-danger">*</span></label>
                                    <div class="mt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="pernah_terjadi_sebelumnya" id="pernah_ya" value="1" {{ old('pernah_terjadi_sebelumnya') == '1' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="pernah_ya">Ya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="pernah_terjadi_sebelumnya" id="pernah_tidak" value="0" {{ old('pernah_terjadi_sebelumnya') == '0' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pernah_tidak">Tidak</label>
                                        </div>
                                    </div>
                                    @error('pernah_terjadi_sebelumnya')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="dokumen_pendukung">Dokumen Pendukung (PDF/JPG/DOC, Maks 5MB)</label>
                                    <input type="file" name="dokumen_pendukung" id="dokumen_pendukung" class="form-control @error('dokumen_pendukung') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    @error('dokumen_pendukung')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('risk-management.incidents.index') }}" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Laporan</button>
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
        console.log('DOM ready, initializing dropdown logic');
        
        // Hapus cache AJAX
        $.ajaxSetup({
            cache: false
        });
        
        // Periksa jika ada nilai jenis insiden yang dipilih saat load halaman (misalnya dari old input)
        var initialTypeId = $('#jenis_insiden_id').val();
        console.log('Initial type ID:', initialTypeId);
        
        if (initialTypeId) {
            loadSubtypes(initialTypeId);
        }
        
        // Event listener untuk perubahan dropdown jenis insiden
        $('#jenis_insiden_id').on('change', function() {
            var incidentTypeId = $(this).val();
            console.log('Jenis insiden berubah:', incidentTypeId);
            
            if (incidentTypeId) {
                loadSubtypes(incidentTypeId);
            } else {
                resetSubtypes();
            }
        });
        
        // Fungsi untuk memuat subtipe
        function loadSubtypes(typeId) {
            console.log('Memuat subtipe untuk jenis insiden ID:', typeId);
            // Tambahkan kelas loading
            $('#incident_subtype_id').addClass('loading-subtypes');
            
            // Log URL yang akan digunakan
            var url = '{{ route("risk-management.incidents.get-subtypes") }}';
            console.log('Requesting URL:', url);
            console.log('Data yang dikirim:', { incident_type_id: typeId });
            
            // Cek URL lengkap
            var fullUrl = url + '?incident_type_id=' + typeId;
            console.log('Full URL constructed:', fullUrl);
            
            // Menggunakan fetch API sebagai alternatif
            fetch(fullUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: 'no-store'
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Response not OK: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Data dari fetch API:', data);
                updateSubtypeDropdown(data);
            })
            .catch(error => {
                console.error('Fetch error:', error);
                resetSubtypes();
                $('#incident_subtype_id').removeClass('loading-subtypes');
                
                // Tampilkan pesan error yang lebih detail
                console.error('Detail error:', error.message);
                
                // Coba fallback ke XMLHttpRequest
                console.log('Mencoba dengan XMLHttpRequest...');
                fallbackXhrRequest(typeId);
            });
        }
        
        // Fungsi fallback menggunakan XMLHttpRequest jika fetch gagal
        function fallbackXhrRequest(typeId) {
            var xhr = new XMLHttpRequest();
            var url = '{{ route("risk-management.incidents.get-subtypes") }}' + '?incident_type_id=' + typeId;
            
            xhr.open('GET', url, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            var data = JSON.parse(xhr.responseText);
                            console.log('Data dari XHR:', data);
                            updateSubtypeDropdown(data);
                        } catch (e) {
                            console.error('Error parsing JSON:', e);
                            alert('Terjadi kesalahan saat memproses data. Silakan coba lagi.');
                        }
                    } else {
                        console.error('XHR error. Status:', xhr.status);
                        alert('Terjadi kesalahan saat memuat data subtipe. Status: ' + xhr.status);
                    }
                    $('#incident_subtype_id').removeClass('loading-subtypes');
                }
            };
            
            xhr.send();
        }
        
        // Fungsi untuk update dropdown
        function updateSubtypeDropdown(data) {
            $('#incident_subtype_id').empty();
            $('#incident_subtype_id').append('<option value="">-- Pilih Subtipe Insiden --</option>');
            
            if (data && data.length > 0) {
                console.log('Memproses ' + data.length + ' subtipe');
                $.each(data, function(key, value) {
                    let selected = '';
                    @if(old('incident_subtype_id'))
                        if (value.id == {{ old('incident_subtype_id') ?? 'null' }}) {
                            selected = 'selected';
                        }
                    @endif
                    console.log('Menambahkan option:', value.id, value.name);
                    $('#incident_subtype_id').append('<option value="'+ value.id +'" '+ selected +'>'+ value.name +'</option>');
                });
            } else {
                console.log('Tidak ada subtipe tersedia untuk jenis insiden ini');
                $('#incident_subtype_id').append('<option value="" disabled>Tidak ada subtipe tersedia</option>');
            }
            
            // Hapus kelas loading
            $('#incident_subtype_id').removeClass('loading-subtypes');
        }
        
        // Fungsi untuk mereset dropdown subtipe
        function resetSubtypes() {
            $('#incident_subtype_id').empty();
            $('#incident_subtype_id').append('<option value="">-- Pilih Subtipe Insiden --</option>');
        }
    });
</script>
@endpush
@endsection 