@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Tambah Data DRP</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('drp.index') }}">DRP</a></div>
                <div class="breadcrumb-item active">Tambah Data</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Form Tambah DRP</h2>
            <p class="section-lead">Pilih ruas jalan terlebih dahulu, kemudian pilih mode input DRP.</p>

            {{-- Card Filter Ruas --}}
            <div class="card">
                <div class="card-header">
                    <h4>Pilih Ruas Jalan</h4>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        {{-- Status Ruas --}}
                        <div class="form-group col-md-3">
                            <label for="filterStatus">Pilih Status Ruas</label>
                            <select id="filterStatus" class="form-control" disabled>
                                @foreach($statusRuas as $status)
                                    <option value="{{ $status->code }}"
                                        {{ $status->code == 'K' ? 'selected' : '' }}>
                                        {{ $status->code_description_ind }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Provinsi --}}
                        <div class="form-group col-md-3">
                            <label for="filterProvinsi">Pilih Provinsi</label>
                            <select id="filterProvinsi" class="form-control" disabled>
                                @foreach($provinsi as $prov)
                                    <option value="{{ $prov->province_code }}"
                                        {{ $prov->province_name == 'Jawa Timur' ? 'selected' : '' }}>
                                        {{ $prov->province_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kabupaten --}}
                        <div class="form-group col-md-3">
                            <label for="filterKabupaten">Pilih Kabupaten</label>
                            <select id="filterKabupaten" class="form-control">
                                <option value="">-- Pilih Kabupaten --</option>
                                @foreach($kabupaten as $kab)
                                    <option value="{{ $kab->kabupaten_code }}">
                                        {{ $kab->kabupaten_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ruas --}}
                        <div class="form-group col-md-3">
                            <label for="filterRuas">Pilih Ruas</label>
                            <select id="filterRuas" class="form-control">
                                <option value="">-- Pilih Kabupaten Dulu --</option>
                            </select>
                        </div>
                    </div>

                    {{-- Info Status DRP --}}
                    <div id="drpStatusInfo" class="alert" style="display: none;"></div>
                </div>
            </div>

            {{-- Card Mode Selection --}}
            <div class="card mt-4" id="modeCard" style="display: none;">
                <div class="card-header">
                    <h4>Pilih Mode Input DRP</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h5><i class="fas fa-magic"></i> Mode Otomatis</h5>
                                </div>
                                <div class="card-body">
                                    <p>Sistem akan otomatis generate semua titik DRP berdasarkan panjang total ruas jalan sesuai standar pola DRP.</p>
                                    <ul class="text-sm">
                                        <li>Start Point (Type 1)</li>
                                        <li>Middle Points setiap 1000m (Type 3)</li>
                                        <li>End Point (Type 2)</li>
                                        <li>Auto-calculate chainage dan description</li>
                                    </ul>
                                    <button type="button" class="btn btn-primary btn-block" onclick="selectMode('auto')">
                                        <i class="fas fa-magic"></i> Pilih Mode Otomatis
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h5><i class="fas fa-edit"></i> Mode Manual</h5>
                                </div>
                                <div class="card-body">
                                    <p>Input data DRP secara manual satu per satu dengan kontrol penuh terhadap semua parameter.</p>
                                    <ul class="text-sm">
                                        <li>Input semua field secara manual</li>
                                        <li>Kontrol penuh terhadap koordinat</li>
                                        <li>Bisa edit existing data</li>
                                        <li>Fleksibilitas tinggi</li>
                                    </ul>
                                    <button type="button" class="btn btn-secondary btn-block" onclick="selectMode('manual')">
                                        <i class="fas fa-edit"></i> Pilih Mode Manual
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form DRP --}}
            <div class="card mt-4" id="formCard" style="display: none;">
                <div class="card-header">
                    <h4 id="formTitle">Form Input DRP</h4>
                    <div class="card-header-action">
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                            <i class="fas fa-undo"></i> Reset Form
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="drpForm" action="{{ route('drp.store') }}" method="POST">
                        @csrf
                        
                        {{-- Hidden Fields --}}
                        <input type="hidden" id="province_code" name="province_code" value="">
                        <input type="hidden" id="kabupaten_code" name="kabupaten_code" value="">
                        <input type="hidden" id="link_no" name="link_no" value="">
                        <input type="hidden" id="generation_mode" name="generation_mode" value="">

                        {{-- Info Ruas Yang Dipilih --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Informasi Ruas Jalan</h6>
                                    <div id="ruasInfo">Silakan pilih ruas jalan terlebih dahulu</div>
                                </div>
                            </div>
                        </div>

                        {{-- Auto Mode Fields --}}
                        <div id="autoModeFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h6><i class="fas fa-magic"></i> Parameter Auto Generate</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="total_length">Panjang Total Ruas (meter) <span class="text-danger">*</span></label>
                                                <input type="number" step="0.01" min="1" class="form-control @error('total_length') is-invalid @enderror" 
                                                       id="total_length" name="total_length" value="{{ old('total_length') }}" 
                                                       placeholder="Contoh: 5570">
                                                @error('total_length')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Masukkan panjang total ruas dalam meter</small>
                                            </div>

                                            <div class="form-group">
                                                <label for="start_chainage">Chainage Awal (KM)</label>
                                                <input type="number" step="0.001" min="0" class="form-control @error('start_chainage') is-invalid @enderror" 
                                                       id="start_chainage" name="start_chainage" value="{{ old('start_chainage', 0) }}" 
                                                       placeholder="Contoh: 0.000">
                                                @error('start_chainage')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Posisi awal dalam kilometer (default: 0)</small>
                                            </div>

                                            <div class="form-group">
                                                <label for="auto_drp_comment">Komentar/Catatan</label>
                                                <textarea class="form-control" id="auto_drp_comment" name="drp_comment" rows="3" 
                                                          placeholder="Masukkan komentar atau catatan tambahan...">{{ old('drp_comment') }}</textarea>
                                                <small class="form-text text-muted">Akan diterapkan ke semua DRP point yang di-generate</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h6><i class="fas fa-info-circle"></i> Preview Generate</h6>
                                        </div>
                                        <div class="card-body" id="generatePreview">
                                            <p class="text-muted">Masukkan panjang total untuk melihat preview</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Manual Mode Fields --}}
                        <div id="manualModeFields" style="display: none;">
                            <div class="row">
                                {{-- Data Utama DRP --}}
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6><i class="fas fa-map-marker-alt"></i> Data Utama DRP</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="drp_order">Urutan DRP <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control @error('drp_order') is-invalid @enderror" 
                                                       id="drp_order" name="drp_order" value="{{ old('drp_order') }}" 
                                                       placeholder="Contoh: 1">
                                                @error('drp_order')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="drp_length">Panjang DRP (m) <span class="text-danger">*</span></label>
                                                <input type="number" step="0.01" class="form-control @error('drp_length') is-invalid @enderror" 
                                                       id="drp_length" name="drp_length" value="{{ old('drp_length') }}" 
                                                       placeholder="Contoh: 100.50">
                                                @error('drp_length')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="drp_type">Tipe DRP <span class="text-danger">*</span></label>
                                                <select class="form-control @error('drp_type') is-invalid @enderror" 
                                                        id="drp_type" name="drp_type">
                                                    <option value="">-- Pilih Tipe DRP --</option>
                                                    @foreach($drpTypes as $type)
                                                        <option value="{{ $type->code }}" 
                                                            {{ old('drp_type') == $type->code ? 'selected' : '' }}>
                                                            {{ $type->code_description_eng }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('drp_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Koordinat DRP --}}
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6><i class="fas fa-compass"></i> Koordinat DRP</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h6 class="text-primary">Koordinat Utara (North)</h6>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="dpr_north_deg">Derajat</label>
                                                        <input type="number" min="0" max="90" class="form-control @error('dpr_north_deg') is-invalid @enderror" 
                                                               id="dpr_north_deg" name="dpr_north_deg" value="{{ old('dpr_north_deg') }}" 
                                                               placeholder="0-90">
                                                        @error('dpr_north_deg')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="dpr_north_min">Menit</label>
                                                        <input type="number" min="0" max="59" class="form-control @error('dpr_north_min') is-invalid @enderror" 
                                                               id="dpr_north_min" name="dpr_north_min" value="{{ old('dpr_north_min') }}" 
                                                               placeholder="0-59">
                                                        @error('dpr_north_min')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="dpr_north_sec">Detik</label>
                                                        <input type="number" step="0.01" min="0" max="59.99" class="form-control @error('dpr_north_sec') is-invalid @enderror" 
                                                               id="dpr_north_sec" name="dpr_north_sec" value="{{ old('dpr_north_sec') }}" 
                                                               placeholder="0.00-59.99">
                                                        @error('dpr_north_sec')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <h6 class="text-success">Koordinat Timur (East)</h6>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="dpr_east_deg">Derajat</label>
                                                        <input type="number" min="0" max="180" class="form-control @error('dpr_east_deg') is-invalid @enderror" 
                                                               id="dpr_east_deg" name="dpr_east_deg" value="{{ old('dpr_east_deg') }}" 
                                                               placeholder="0-180">
                                                        @error('dpr_east_deg')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="dpr_east_min">Menit</label>
                                                        <input type="number" min="0" max="59" class="form-control @error('dpr_east_min') is-invalid @enderror" 
                                                               id="dpr_east_min" name="dpr_east_min" value="{{ old('dpr_east_min') }}" 
                                                               placeholder="0-59">
                                                        @error('dpr_east_min')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="dpr_east_sec">Detik</label>
                                                        <input type="number" step="0.01" min="0" max="59.99" class="form-control @error('dpr_east_sec') is-invalid @enderror" 
                                                               id="dpr_east_sec" name="dpr_east_sec" value="{{ old('dpr_east_sec') }}" 
                                                               placeholder="0.00-59.99">
                                                        @error('dpr_east_sec')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Deskripsi Manual Mode --}}
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6><i class="fas fa-file-alt"></i> Deskripsi & Keterangan</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="manual_drp_desc">Deskripsi DRP</label>
                                                <textarea class="form-control @error('drp_desc') is-invalid @enderror" 
                                                          id="manual_drp_desc" name="drp_desc" rows="3" 
                                                          placeholder="Masukkan deskripsi DRP...">{{ old('drp_desc') }}</textarea>
                                                @error('drp_desc')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Maksimal 500 karakter</small>
                                            </div>

                                            <div class="form-group">
                                                <label for="manual_drp_comment">Komentar/Catatan</label>
                                                <textarea class="form-control @error('drp_comment') is-invalid @enderror" 
                                                          id="manual_drp_comment" name="drp_comment" rows="3" 
                                                          placeholder="Masukkan komentar atau catatan tambahan...">{{ old('drp_comment') }}</textarea>
                                                @error('drp_comment')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Maksimal 1000 karakter</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg mr-2" id="submitBtn">
                                        <i class="fas fa-save"></i> Simpan Data DRP
                                    </button>
                                    <a href="{{ route('drp.index') }}" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Existing Data DRP (Jika Ada) --}}
            <div class="card mt-4" id="existingDataCard" style="display: none;">
                <div class="card-header">
                    <h4><i class="fas fa-database"></i> Data DRP Yang Sudah Ada</h4>
                </div>
                <div class="card-body">
                    <div id="existingDrpTable">
                        <!-- Data akan dimuat via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Event handler untuk perubahan kabupaten
    $('#filterKabupaten').on('change', function() {
        let kabupatenCode = $(this).val();
        
        if (kabupatenCode) {
            loadRuasJalan(kabupatenCode);
        } else {
            resetRuasDropdown();
            hideModeCard();
            hideFormCard();
        }
        
        resetForm();
        hideExistingDataCard();
    });

    // Event handler untuk perubahan ruas
    $('#filterRuas').on('change', function() {
        let linkNo = $(this).val();
        
        if (linkNo) {
            handleRuasSelection(linkNo);
        } else {
            hideModeCard();
            hideFormCard();
            hideExistingDataCard();
        }
    });

    // Event handler untuk preview auto generate
    $('#total_length, #start_chainage').on('input', function() {
        generatePreview();
    });

    // Function untuk load ruas jalan berdasarkan kabupaten
    function loadRuasJalan(kabupatenCode) {
        $('#filterRuas').html('<option value="">Loading...</option>').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('drp.getLinks') }}",
            type: "GET",
            data: { kabupaten_code: kabupatenCode },
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">-- Pilih Ruas --</option>';
                    
                    response.data.forEach(function(ruas) {
                        options += `<option value="${ruas.link_no}">${ruas.link_code} - ${ruas.link_name}</option>`;
                    });
                    
                    $('#filterRuas').html(options).prop('disabled', false);
                } else {
                    showAlert('error', 'Gagal memuat data ruas jalan');
                    resetRuasDropdown();
                }
            },
            error: function() {
                showAlert('error', 'Terjadi kesalahan saat memuat ruas jalan');
                resetRuasDropdown();
            }
        });
    }

    // Function untuk handle pemilihan ruas
    function handleRuasSelection(linkNo) {
        // Set data hidden fields
        $('#province_code').val($('#filterProvinsi').val());
        $('#kabupaten_code').val($('#filterKabupaten').val());
        $('#link_no').val(linkNo);
        
        // Update info ruas
        updateRuasInfo(linkNo);
        
        // Cek apakah ruas sudah ada DRP atau belum
        checkDRPExists(linkNo);
    }

    // Function untuk cek apakah DRP sudah ada
    function checkDRPExists(linkNo) {
        $.ajax({
            url: "{{ route('drp.getDetail') }}",
            type: "GET", 
            data: { link_no: linkNo },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    // Ruas sudah ada DRP - tampilkan data existing dan langsung form manual
                    showDRPStatusInfo('warning', 'Ruas ini sudah memiliki data DRP. Anda akan masuk ke mode manual untuk mengedit data yang sudah ada.');
                    showExistingData(response.data);
                    selectMode('manual');
                    populateFormWithExistingData(response.data[0]);
                } else {
                    // Ruas belum ada DRP - tampilkan pilihan mode
                    showDRPStatusInfo('success', 'Ruas ini belum memiliki data DRP. Silakan pilih mode input yang diinginkan.');
                    hideExistingDataCard();
                    showModeCard();
                }
            },
            error: function() {
                showAlert('error', 'Terjadi kesalahan saat memeriksa data DRP');
                hideModeCard();
                hideFormCard();
            }
        });
    }

    // Function untuk memilih mode
    window.selectMode = function(mode) {
        $('#generation_mode').val(mode);
        
        if (mode === 'auto') {
            showAutoMode();
            $('#submitBtn').html('<i class="fas fa-magic"></i> Generate DRP Points');
        } else {
            showManualMode();
            $('#submitBtn').html('<i class="fas fa-save"></i> Simpan Data DRP');
        }
        
        hideModeCard();
        showFormCard();
    };

    // Function untuk generate preview
function generatePreview() {
    const totalLength = parseFloat($('#total_length').val()) || 0;
    const startChainage = parseFloat($('#start_chainage').val()) || 0;
    
    if (totalLength <= 0) {
        $('#generatePreview').html('<p class="text-muted">Masukkan panjang total untuk melihat preview</p>');
        return;
    }
    
    let preview = '<h6>Preview DRP Points:</h6>';
    preview += '<table class="table table-sm table-bordered">';
    preview += '<thead><tr><th>Order</th><th>Type</th><th>Chainage</th><th>Length</th><th>Desc</th></tr></thead>';
    preview += '<tbody>';
    
    let currentChainage = startChainage;
    let order = 1;
    let remainingLength = totalLength;
    
    // Start Point - sama seperti di controller
    let firstSegmentLength = 1000 - ((startChainage * 1000) % 1000);
    if (firstSegmentLength === 1000) {
        firstSegmentLength = Math.min(1000, remainingLength);
    } else {
        firstSegmentLength = Math.min(firstSegmentLength, remainingLength);
    }
    
    preview += `<tr class="table-success">
        <td>${order}</td>
        <td>Start (1)</td>
        <td>${currentChainage.toFixed(3)}</td>
        <td>${firstSegmentLength.toFixed(0)}m</td>
        <td>${formatChainage(currentChainage)}</td>
    </tr>`;
    
    currentChainage += firstSegmentLength / 1000;
    remainingLength -= firstSegmentLength;
    order++;
    
    // Middle Points
    while (remainingLength > 0) {
        const segmentLength = Math.min(1000, remainingLength);
        preview += `<tr class="table-info">
            <td>${order}</td>
            <td>Middle (3)</td>
            <td>${currentChainage.toFixed(3)}</td>
            <td>${segmentLength.toFixed(0)}m</td>
            <td>${formatChainage(currentChainage)}</td>
        </tr>`;
        
        currentChainage += segmentLength / 1000;
        remainingLength -= segmentLength;
        order++;
    }
    
    // End Point
    preview += `<tr class="table-warning">
        <td>${order}</td>
        <td>End (2)</td>
        <td>${currentChainage.toFixed(3)}</td>
        <td>0m</td>
        <td>${formatChainage(currentChainage)}</td>
    </tr>`;
    
    preview += '</tbody></table>';
    preview += `<p class="text-info"><i class="fas fa-info-circle"></i> Total ${order} DRP points akan di-generate</p>`;
    
    $('#generatePreview').html(preview);
}
    
    // Function untuk format chainage
    function formatChainage(chainage) {
        const km = Math.floor(chainage);
        const meter = Math.round((chainage - km) * 1000);
        return `${km}+${meter.toString().padStart(3, '0')}`;
    }

    // Function untuk populate form dengan data existing
    function populateFormWithExistingData(data) {
        $('#drp_num').val(data.drp_num || '');
        $('#chainage').val(data.chainage || '');
        $('#drp_order').val(data.drp_order || '');
        $('#drp_length').val(data.drp_length || '');
        $('#drp_type').val(data.drp_type || '');
        $('#dpr_north_deg').val(data.dpr_north_deg || '');
        $('#dpr_north_min').val(data.dpr_north_min || '');
        $('#dpr_north_sec').val(data.dpr_north_sec || '');
        $('#dpr_east_deg').val(data.dpr_east_deg || '');
        $('#dpr_east_min').val(data.dpr_east_min || '');
        $('#dpr_east_sec').val(data.dpr_east_sec || '');
        $('#manual_drp_desc').val(data.drp_desc || '');
        $('#manual_drp_comment').val(data.drp_comment || '');
    }

    // Function untuk update info ruas yang dipilih
    function updateRuasInfo(linkNo) {
        let ruasText = $('#filterRuas option:selected').text();
        let provinsiText = $('#filterProvinsi option:selected').text();
        let kabupatenText = $('#filterKabupaten option:selected').text();
        
        let infoHtml = `
            <strong>Ruas Terpilih:</strong> ${ruasText}<br>
            <strong>Provinsi:</strong> ${provinsiText}<br>
            <strong>Kabupaten:</strong> ${kabupatenText}
        `;
        
        $('#ruasInfo').html(infoHtml);
    }

    // Function untuk show existing data
    function showExistingData(data) {
        let tableHtml = `
            <table class="table table-striped table-bordered" id="existingDrpDataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor DRP</th>
                        <th>Chainage</th>
                        <th>Panjang DRP</th>
                        <th>Tipe DRP</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        data.forEach(function(item, index) {
            tableHtml += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.drp_num || '-'}</td>
                    <td>${item.chainage || '-'}</td>
                    <td>${item.drp_length || '-'}m</td>
                    <td>${item.type?.code_description_eng || '-'}</td>
                    <td>${item.drp_desc || '-'}</td>
                </tr>
            `;
        });
        
        tableHtml += `</tbody></table>`;
        
        $('#existingDrpTable').html(tableHtml);
        showExistingDataCard();
        
        // Initialize DataTable
        $('#existingDrpDataTable').DataTable({
            responsive: true,
            pageLength: 5,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            }
        });
    }

    // UI Helper Functions
    function showModeCard() {
        $('#modeCard').slideDown();
    }

    function hideModeCard() {
        $('#modeCard').slideUp();
    }

    function showFormCard() {
        $('#formCard').slideDown();
    }

    function hideFormCard() {
        $('#formCard').slideUp();
    }

    function showAutoMode() {
        $('#autoModeFields').slideDown();
        $('#manualModeFields').slideUp();
        $('#formTitle').html('<i class="fas fa-magic"></i> Form Auto Generate DRP');
    }

    function showManualMode() {
        $('#manualModeFields').slideDown();
        $('#autoModeFields').slideUp();
        $('#formTitle').html('<i class="fas fa-edit"></i> Form Manual Input DRP');
    }

    function showExistingDataCard() {
        $('#existingDataCard').slideDown();
    }

    function hideExistingDataCard() {
        $('#existingDataCard').slideUp();
    }

    function showDRPStatusInfo(type, message) {
        let alertClass = type === 'success' ? 'alert-success' : 'alert-warning';
        let icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        $('#drpStatusInfo')
            .removeClass('alert-success alert-warning alert-danger')
            .addClass(`alert-${type}`)
            .html(`<i class="fas ${icon}"></i> ${message}`)
            .slideDown();
    }

    function resetRuasDropdown() {
        $('#filterRuas').html('<option value="">-- Pilih Kabupaten Dulu --</option>').prop('disabled', false);
    }

    function resetFormFields() {
        $('#drpForm')[0].reset();
        // Tetap pertahankan hidden fields
        $('#province_code').val($('#filterProvinsi').val());
        $('#kabupaten_code').val($('#filterKabupaten').val()); 
        $('#link_no').val($('#filterRuas').val());
        $('#generation_mode').val('');
    }

    function resetForm() {
        resetFormFields();
        $('#drpStatusInfo').slideUp();
        $('#generatePreview').html('<p class="text-muted">Masukkan panjang total untuk melihat preview</p>');
    }

    function showAlert(type, message) {
        let alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        let alert = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                     </div>`;
        
        $('.section-body').prepend(alert);
        
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }

    // Global reset function
    window.resetForm = function() {
        resetForm();
        hideModeCard();
        hideFormCard();
        hideExistingDataCard();
    };
});
</script>
@endpush