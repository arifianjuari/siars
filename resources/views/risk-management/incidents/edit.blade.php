@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Edit Insiden Keselamatan Pasien</h4>
                    <a href="{{ route('risk-management.incidents.show', $incident->id) }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Detail
                    </a>
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
                    <form action="{{ route('risk-management.incidents.update', $incident->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_waktu_kejadian">Tanggal & Waktu Kejadian <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="tanggal_waktu_kejadian" id="tanggal_waktu_kejadian" class="form-control @error('tanggal_waktu_kejadian') is-invalid @enderror" required value="{{ old('tanggal_waktu_kejadian', $incident->tanggal_waktu_kejadian ? $incident->tanggal_waktu_kejadian->format('Y-m-d\TH:i') : '') }}">
                                    @error('tanggal_waktu_kejadian')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location_id">Lokasi Kejadian <span class="text-danger">*</span></label>
                                    <select name="location_id" id="location_id" class="form-control @error('location_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Lokasi --</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('location_id', $incident->location_id) == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_pasien">Nama Pasien / Inisial <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_pasien" id="nama_pasien" class="form-control @error('nama_pasien') is-invalid @enderror" required value="{{ old('nama_pasien', $incident->nama_pasien) }}">
                                    @error('nama_pasien')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="no_rm">Nomor Rekam Medis <span class="text-danger">*</span></label>
                                    <input type="text" name="no_rm" id="no_rm" class="form-control @error('no_rm') is-invalid @enderror" required value="{{ old('no_rm', $incident->no_rm) }}">
                                    @error('no_rm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="incident_type_id">Jenis Insiden <span class="text-danger">*</span></label>
                                    <select name="incident_type_id" id="incident_type_id" class="form-control @error('incident_type_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Jenis Insiden --</option>
                                        @foreach($incidentTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('incident_type_id', $incident->incident_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }} {{ $type->code ? '('.$type->code.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('incident_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="incident_subtype_id">Subtipe Insiden <span class="text-danger">*</span></label>
                                    <select name="incident_subtype_id" id="incident_subtype_id" class="form-control @error('incident_subtype_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Subtipe Insiden --</option>
                                        @if($incident->incident_subtype_id)
                                            <option value="{{ $incident->incident_subtype_id }}" selected>{{ $incident->incidentSubtype->name }}</option>
                                        @endif
                                    </select>
                                    @error('incident_subtype_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="kronologis">Kronologis Singkat <span class="text-danger">*</span></label>
                                    <textarea name="kronologis" id="kronologis" rows="4" class="form-control @error('kronologis') is-invalid @enderror" required>{{ old('kronologis', $incident->kronologis) }}</textarea>
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
                                    <textarea name="tindakan_langsung" id="tindakan_langsung" rows="3" class="form-control @error('tindakan_langsung') is-invalid @enderror" required>{{ old('tindakan_langsung', $incident->tindakan_langsung) }}</textarea>
                                    @error('tindakan_langsung')
                                        <div class="invalid-feedback">{{ $message }}</div>
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
                                    
                                    @if($incident->dokumen_pendukung)
                                        <div class="mt-2">
                                            <span class="text-muted">Dokumen saat ini:</span>
                                            <button type="button" class="btn btn-sm btn-info ms-2" data-bs-toggle="modal" data-bs-target="#previewDocumentModal">
                                                <i class="fas fa-eye"></i> Lihat Dokumen
                                            </button>
                                            <a href="{{ asset('storage/' . $incident->dokumen_pendukung) }}" target="_blank" class="btn btn-sm btn-secondary ms-2">
                                                <i class="fas fa-file-download"></i> Unduh
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#deleteDocumentModal">
                                                <i class="fas fa-trash"></i> Hapus Dokumen
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('risk-management.incidents.show', $incident->id) }}" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
        var initialTypeId = $('#incident_type_id').val();
        console.log('Initial type ID:', initialTypeId);
        
        if (initialTypeId) {
            loadSubtypes(initialTypeId);
        }
        
        // Event listener untuk perubahan dropdown jenis insiden
        $('#incident_type_id').on('change', function() {
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
            var currentSubtypeId = '{{ $incident->incident_subtype_id }}';
            
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
                    @else
                        if (value.id == currentSubtypeId) {
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

<!-- Modal Konfirmasi Hapus Dokumen -->
<div class="modal fade" id="deleteDocumentModal" tabindex="-1" aria-labelledby="deleteDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteDocumentModalLabel">Konfirmasi Hapus Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus dokumen pendukung ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('risk-management.incidents.delete-document', $incident->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Dokumen</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Dokumen -->
<div class="modal fade" id="previewDocumentModal" tabindex="-1" aria-labelledby="previewDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="previewDocumentModalLabel">Preview Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                @if($incident->dokumen_pendukung)
                    @php
                        $file_extension = pathinfo($incident->dokumen_pendukung, PATHINFO_EXTENSION);
                        $is_image = in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif']);
                        $is_pdf = strtolower($file_extension) === 'pdf';
                        $file_url = asset('storage/' . $incident->dokumen_pendukung);
                    @endphp

                    @if($is_image)
                        <div class="text-center p-3">
                            <img src="{{ $file_url }}" class="img-fluid" alt="Dokumen Pendukung">
                        </div>
                    @elseif($is_pdf)
                        <div class="ratio ratio-16x9" style="min-height: 500px;">
                            <iframe src="{{ $file_url }}" allowfullscreen></iframe>
                        </div>
                    @else
                        <div class="p-5 text-center">
                            <div class="mb-3">
                                <i class="fas fa-file-alt fa-5x text-primary"></i>
                            </div>
                            <h5>Dokumen tidak dapat ditampilkan langsung</h5>
                            <p class="text-muted mb-4">Format file {{ strtoupper($file_extension) }} tidak dapat ditampilkan sebagai preview. Silakan unduh dokumen untuk melihatnya.</p>
                            <a href="{{ $file_url }}" class="btn btn-primary" download>
                                <i class="fas fa-download me-2"></i> Unduh Dokumen
                            </a>
                        </div>
                    @endif
                @else
                    <div class="p-5 text-center">
                        <h5>Tidak ada dokumen</h5>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ asset('storage/' . $incident->dokumen_pendukung) }}" class="btn btn-primary" download>
                    <i class="fas fa-download me-1"></i> Unduh
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 