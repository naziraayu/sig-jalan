@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Data Inventarisasi Jalan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('inventarisasi-jalan.index') }}">Inventarisasi Jalan</a></div>
                <div class="breadcrumb-item active">Edit Data</div>
            </div>
        </div>

        <div class="section-body">
            
            {{-- Alert info --}}
            <div class="alert alert-info alert-has-icon">
                <div class="alert-icon"><i class="fas fa-info-circle"></i></div>
                <div class="alert-body">
                    <div class="alert-title">Mode Edit</div>
                    Anda sedang mengedit data inventarisasi untuk <strong>{{ $link->linkMaster?->link_code ?? $link->link_no }} - {{ $link->linkMaster?->link_name ?? 'Tidak ada nama' }}</strong> tahun <strong>{{ $selectedYear }}</strong>. 
                    Data lama akan diganti dengan data baru yang Anda input.
                </div>
            </div>

            {{-- ==================== STEP 1: Setup Survey ==================== --}}
            <div id="step1" class="card" style="max-width: 900px; margin: 0 auto;">
                <div class="card-header bg-warning text-white">
                    <h4><i class="fas fa-road"></i> ROAD SURVEY - Edit Selection</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4">
                        <a href="{{ route('inventarisasi-jalan.show', $link->link_no) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-chevron-left"></i> Kembali ke Detail
                        </a>
                        <h3 class="text-center mb-0">Edit Inventarisasi Jalan</h3>
                        <button type="button" id="btnNext" class="btn btn-primary">
                            Selanjutnya <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <hr>

                    {{-- Form Fields --}}
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label font-weight-bold">
                            Nomor Ruas <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-8">
                            <select id="linkNo" class="form-control" disabled>
                                <option value="{{ $link->link_no }}" 
                                        data-link-id="{{ $link->id }}"
                                        data-link-code="{{ $link->linkMaster?->link_code ?? $link->link_no }}"
                                        data-link-name="{{ $link->linkMaster?->link_name ?? 'Tidak ada nama' }}"
                                        data-link-length="{{ $link->link_length_official ?? 0 }}"
                                        data-province="{{ $link->province_code }}"
                                        data-kabupaten="{{ $link->kabupaten_code }}" selected>
                                    {{ $link->linkMaster?->link_code ?? $link->link_no }}
                                </option>
                            </select>
                            <small class="text-muted">Nomor ruas tidak dapat diubah saat edit</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label font-weight-bold">Nama Ruas</label>
                        <div class="col-sm-8">
                            <input type="text" id="namaRuas" class="form-control bg-light" 
                                   value="{{ $link->linkMaster?->link_name ?? 'Tidak ada nama' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label font-weight-bold">Panjang Ruas (km)</label>
                        <div class="col-sm-8">
                            <input type="text" id="panjangRuas" class="form-control bg-light" 
                                   value="{{ number_format($link->link_length_official ?? 0, 2) }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label font-weight-bold">
                            Survei oleh <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" id="surveyorName" class="form-control" 
                                   placeholder="Nama Surveyor" value="{{ auth()->user()->name }}" required>
                        </div>
                        <label class="col-sm-2 col-form-label font-weight-bold text-right">
                            Tanggal Survei <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-2">
                            <input type="date" id="surveyDate" class="form-control" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label font-weight-bold">Survei oleh</label>
                        <div class="col-sm-8">
                            <input type="text" id="surveyorName2" class="form-control" 
                                   placeholder="Nama Surveyor 2 (Opsional)">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label font-weight-bold">
                            Arah <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-8">
                            <select id="direction" class="form-control" required>
                                <option value="Normal">Normal</option>
                                <option value="Reverse">Reverse</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label font-weight-bold">
                            Interval (m) <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-8">
                            <select id="interval" class="form-control" required>
                                <option value="100" selected>100</option>
                                <option value="200">200</option>
                            </select>
                            <small class="text-muted">Jarak interval pengukuran dalam meter</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label font-weight-bold">Pengumpulan Data</label>
                        <div class="col-sm-8">
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn btn-outline-success active">
                                    <input type="radio" name="data_type" value="Perkerasan" checked> Perkerasan
                                </label>
                                <label class="btn btn-outline-secondary disabled" style="opacity: 0.5; cursor: not-allowed;">
                                    <input type="radio" disabled> Kiri
                                </label>
                                <label class="btn btn-outline-secondary disabled" style="opacity: 0.5; cursor: not-allowed;">
                                    <input type="radio" disabled> Kanan
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('inventarisasi-jalan.show', $link->link_no) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="button" id="btnNextFooter" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> Selanjutnya
                    </button>
                </div>
            </div>

            {{-- ==================== STEP 2: INPUT DETAIL ==================== --}}
            <div id="step2" style="display: none;">
                <div class="row">
                    {{-- Kiri: Info Ruas --}}
                    <div class="col-md-3">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h4>Informasi Ruas</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <td class="font-weight-bold">Ruas</td>
                                        <td id="infoRuas">-</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Panjang</td>
                                        <td id="infoPanjang">-</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Arah</td>
                                        <td id="infoArah">-</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Interval (m)</td>
                                        <td id="infoInterval">-</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Total Segmen</td>
                                        <td id="infoTotalSegmen" class="text-primary">0</td>
                                    </tr>
                                </table>
                                <button type="button" id="btnBack" class="btn btn-secondary btn-block">
                                    <i class="fas fa-chevron-left"></i> Kembali
                                </button>
                                <button type="button" id="btnMuatDataLama" class="btn btn-info btn-block">
                                    <i class="fas fa-download"></i> Muat Data Lama
                                </button>
                                <button type="button" id="btnHapusSemua" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash"></i> Hapus Semua
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Kanan: Form Input --}}
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header bg-warning text-white d-flex justify-content-between">
                                <h4 class="mb-0">Edit Inventarisasi Jalan</h4>
                            </div>
                            <div class="card-body">
                                {{-- Tabs Perkerasan/Kiri/Kanan --}}
                                <div class="btn-group mb-3" role="group">
                                    <button type="button" class="btn btn-outline-secondary disabled">Kiri</button>
                                    <button type="button" class="btn btn-success">Perkerasan</button>
                                    <button type="button" class="btn btn-outline-secondary disabled">Kanan</button>
                                </div>

                                {{-- Form Input Data --}}
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Data Masukan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Kolom Kiri --}}
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Dari (m)</label>
                                                            <input type="number" id="inputDari" class="form-control" value="0" step="1">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Ke (m)</label>
                                                            <input type="number" id="inputKe" class="form-control" step="1">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Tipe Perkerasan</label>
                                                    <select id="inputTipePerkerasan" class="form-control">
                                                        <option value="">-- Pilih Tipe --</option>
                                                        @foreach($pavementTypes as $type)
                                                            <option value="{{ $type->code }}">{{ $type->code_description_ind }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Lebar Perkerasan (m)</label>
                                                    <select id="inputLebar" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="3">3</option>
                                                        <option value="3.5">3.5</option>
                                                        <option value="4">4</option>
                                                        <option value="4.5">4.5</option>
                                                        <option value="5">5</option>
                                                        <option value="5.5">5.5</option>
                                                        <option value="6">6</option>
                                                        <option value="6.5">6.5</option>
                                                        <option value="7">7</option>
                                                        <option value="10">10</option>
                                                        <option value="12">12</option>
                                                        <option value="14">14</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Lebar RuMiJa (m)</label>
                                                    <select id="inputRumijaLebar" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="7">7</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                        <option value="13">13</option>
                                                        <option value="14">14</option>
                                                        <option value="15">15</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Kolom Kanan --}}
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Medan Jalan</label>
                                                    <select id="inputMedanJalan" class="form-control">
                                                        <option value="">-- Pilih --</option>
                                                        @foreach($terrainTypes as $terrain)
                                                            <option value="{{ $terrain->code }}">{{ $terrain->code_description_ind }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Tak Dapat Dilalui</label>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="inputTakDapatDilalui">
                                                        <label class="custom-control-label" for="inputTakDapatDilalui">Ya</label>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Alasan Tak Dapat Dilalui</label>
                                                    <select id="inputAlasanTakDapatDilalui" class="form-control">
                                                        <option value="">-- Pilih Alasan --</option>
                                                        @foreach($impassableReasons as $reason)
                                                            <option value="{{ $reason->code }}">{{ $reason->code_description_ind }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right mt-3">
                                            <button type="button" id="btnTambah" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Tambah
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tabel Preview Data --}}
                                <div class="table-responsive mt-4">
                                    <table id="tabelPreview" class="table table-bordered table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Dari</th>
                                                <th>Ke</th>
                                                <th>Perkerasan</th>
                                                <th>Lebar (m)</th>
                                                <th>RuMiJa</th>
                                                <th>Medan Jal.</th>
                                                <th>Tak Dapat †</th>
                                                <th>Alasan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyPreview">
                                            <tr id="emptyRow">
                                                <td colspan="9" class="text-center text-muted">Belum ada data. Klik "Muat Data Lama" untuk memuat data yang sudah ada.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-right mt-3">
                                    <button type="button" id="btnSimpanSemua" class="btn btn-success btn-lg">
                                        <i class="fas fa-save"></i> Update Semua Data
                                    </button>
                                </div>
                            </div>
                        </div>
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
    
    // ==================== VARIABLES ====================
    let surveySetup = {};
    let inventoryData = [];
    let panjangRuasKm = 0;
    let interval = 100;
    
    // ✅ Data lama dari database (untuk fitur "Muat Data Lama")
    let existingData = @json($existingData);
    
    // ==================== STEP 1: SETUP ====================
    
    // Auto-load data ruas yang dipilih
    (function() {
        const selectedOption = $('#linkNo option:selected');
        const linkId = selectedOption.data('link-id');
        const linkName = selectedOption.data('link-name');
        const linkLength = selectedOption.data('link-length');
        
        surveySetup.link_id = linkId;
        surveySetup.link_no = $('#linkNo').val();
        surveySetup.link_code = selectedOption.data('link-code');
        surveySetup.link_name = linkName;
        surveySetup.link_length = linkLength;
        surveySetup.province_code = selectedOption.data('province');
        surveySetup.kabupaten_code = selectedOption.data('kabupaten');
        
        panjangRuasKm = parseFloat(linkLength);
    })();
    
    // Tombol Next
    $('#btnNext, #btnNextFooter').on('click', function() {
        // Validasi
        if (!$('#surveyorName').val()) {
            Swal.fire('Error', 'Nama Surveyor wajib diisi!', 'error');
            return;
        }
        if (!$('#surveyDate').val()) {
            Swal.fire('Error', 'Tanggal Survei wajib diisi!', 'error');
            return;
        }
        
        // Simpan data setup
        surveySetup.surveyor_name = $('#surveyorName').val();
        surveySetup.surveyor_name_2 = $('#surveyorName2').val();
        surveySetup.survey_date = $('#surveyDate').val();
        surveySetup.direction = $('#direction').val();
        surveySetup.interval = parseInt($('#interval').val());
        surveySetup.data_collection_type = 'Perkerasan';
        surveySetup.year = {{ $selectedYear }};
        
        interval = surveySetup.interval;
        
        // Tampilkan step 2
        $('#step1').hide();
        $('#step2').show();
        
        // Update info ruas
        $('#infoRuas').html(`<strong>${surveySetup.link_code}</strong><br><small>${surveySetup.link_name}</small>`);
        $('#infoPanjang').html(`<strong>${parseFloat(surveySetup.link_length).toFixed(2)} km</strong>`);
        $('#infoArah').text(surveySetup.direction);
        $('#infoInterval').text(surveySetup.interval);
        
        // Set nilai awal Ke
        $('#inputKe').val(interval);
    });
    
    // Tombol Back
    $('#btnBack').on('click', function() {
        $('#step2').hide();
        $('#step1').show();
    });
    
    // ==================== STEP 2: INPUT DETAIL ====================

    // ✅ FITUR BARU: Muat Data Lama
    $('#btnMuatDataLama').on('click', function() {
        if (inventoryData.length > 0) {
            Swal.fire({
                title: 'Muat Data Lama?',
                text: 'Data yang sudah diinput akan diganti dengan data lama dari database',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Muat Data Lama',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    loadExistingData();
                }
            });
        } else {
            loadExistingData();
        }
    });
    
    function loadExistingData() {
        inventoryData = [];
        
        existingData.forEach(function(item) {
            const dataItem = {
                chainage_from: parseFloat(item.chainage_from),
                chainage_to: parseFloat(item.chainage_to),
                pave_type: item.pave_type,
                pave_type_text: item.pavement_type?.code_description_ind || '-',
                pave_width: item.pave_width,
                row: item.row,
                terrain: item.terrain,
                terrain_text: item.terrain_type?.code_description_ind || '-',
                impassable: item.impassable,
                impassable_reason: item.impassable_reason,
                impassable_reason_text: item.impassable_reason ? (item.impassable_reason_data?.code_description_ind || '-') : '-'
            };
            inventoryData.push(dataItem);
        });
        
        renderTable();
        
        // Set input Dari/Ke ke segmen terakhir + interval
        if (inventoryData.length > 0) {
            const lastSegment = inventoryData[inventoryData.length - 1];
            const nextDari = lastSegment.chainage_to;
            let nextKe = nextDari + interval;
            const maxMeter = panjangRuasKm;
            
            if (nextKe > maxMeter) {
                nextKe = maxMeter;
            }
            
            $('#inputDari').val(nextDari);
            $('#inputKe').val(nextKe);
        }
        
        Swal.fire({
            icon: 'success',
            title: 'Data Dimuat',
            text: `${inventoryData.length} segmen berhasil dimuat dari database`,
            timer: 2000,
            showConfirmButton: false
        });
    }

    // Tombol Tambah
    $('#btnTambah').on('click', function() {
        const dari = parseFloat($('#inputDari').val());
        const ke = parseFloat($('#inputKe').val());
        const tipePerkerasan = $('#inputTipePerkerasan').val();
        const tipeText = $('#inputTipePerkerasan option:selected').text();
        const lebar = $('#inputLebar').val();
        const rumijaLebar = $('#inputRumijaLebar').val();
        const medanJalan = $('#inputMedanJalan').val();
        const medanText = $('#inputMedanJalan option:selected').text();
        const takDapatDilalui = $('#inputTakDapatDilalui').is(':checked');
        const alasanTakDapatDilalui = $('#inputAlasanTakDapatDilalui').val();
        const alasanText = $('#inputAlasanTakDapatDilalui option:selected').text();
        
        // Validasi
        if (isNaN(dari) || isNaN(ke)) {
            Swal.fire('Error', 'Nilai Dari dan Ke harus diisi!', 'error');
            return;
        }
        if (ke <= dari) {
            Swal.fire('Error', 'Nilai Ke harus lebih besar dari Dari!', 'error');
            return;
        }
        
        const maxMeter = panjangRuasKm * 1000;
        if (ke > maxMeter) {
            Swal.fire('Error', `Nilai Ke tidak boleh melebihi panjang ruas (${maxMeter} m)!`, 'error');
            return;
        }
        
        if (!tipePerkerasan) {
            Swal.fire('Error', 'Tipe Perkerasan wajib diisi!', 'error');
            return;
        }
        
        if (takDapatDilalui && !alasanTakDapatDilalui) {
            Swal.fire('Error', 'Jika Tak Dapat Dilalui dicentang, alasan harus dipilih!', 'error');
            return;
        }
        
        // Tambah ke array
        const dataItem = {
            chainage_from: dari,
            chainage_to: ke,
            pave_type: tipePerkerasan,
            pave_type_text: tipeText,
            pave_width: lebar || null,
            row: rumijaLebar || null,
            terrain: medanJalan || null,
            terrain_text: medanText,
            impassable: takDapatDilalui ? 1 : 0,
            impassable_reason: takDapatDilalui ? alasanTakDapatDilalui : null,
            impassable_reason_text: takDapatDilalui ? alasanText : '-'
        };
        
        inventoryData.push(dataItem);
        
        renderTable();
        
        // Auto increment
        const nextDari = ke;
        let nextKe = ke + interval;
        
        if (nextKe > maxMeter) {
            nextKe = maxMeter;
        }
        
        $('#inputDari').val(nextDari);
        $('#inputKe').val(nextKe);

        $('#inputTakDapatDilalui').prop('checked', false);
        $('#inputAlasanTakDapatDilalui').val('');

        if (nextKe >= maxMeter) {
            Swal.fire({
                icon: 'info',
                title: 'Segmen Terakhir',
                text: 'Anda telah mencapai akhir ruas jalan.',
                confirmButtonColor: '#28a745'
            });
            $('#inputDari').prop('readonly', true);
            $('#inputKe').prop('readonly', true);
        } else {
            $('#btnTambah').focus();
        }
    });

    // Render tabel
    function renderTable() {
        $('#emptyRow').hide();
        $('#tbodyPreview').empty();
        
        inventoryData.forEach((item, index) => {
            const row = `
                <tr>
                    <td>${item.chainage_from}</td>
                    <td>${item.chainage_to}</td>
                    <td><small>${item.pave_type_text}</small></td>
                    <td class="text-center">${item.pave_width || '-'}</td>
                    <td class="text-center">${item.row || '-'}</td>
                    <td><small>${item.terrain_text || '-'}</small></td>
                    <td class="text-center">
                        ${item.impassable ? '<i class="fas fa-check-square text-danger"></i>' : '<i class="far fa-square"></i>'}
                    </td>
                    <td><small>${item.impassable_reason_text}</small></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" onclick="hapusData(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#tbodyPreview').append(row);
        });
        
        // Update info total segmen
        $('#infoTotalSegmen').text(inventoryData.length);
        
        if (inventoryData.length === 0) {
            $('#emptyRow').show();
            $('#infoTotalSegmen').text(0);
        }
    }
    
    // Hapus satu data
    window.hapusData = function(index) {
        Swal.fire({
            title: 'Hapus Data?',
            text: 'Data ini akan dihapus dari daftar',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                inventoryData.splice(index, 1);
                renderTable();
            }
        });
    };
    
    // Hapus semua
    $('#btnHapusSemua').on('click', function() {
        if (inventoryData.length === 0) {
            Swal.fire('Info', 'Belum ada data yang diinput', 'info');
            return;
        }
        
        Swal.fire({
            title: 'Hapus Semua Data?',
            text: `${inventoryData.length} data akan dihapus dari daftar`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus Semua',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                inventoryData = [];
                renderTable();
                $('#inputDari').val(0).prop('readonly', false);
                $('#inputKe').val(interval).prop('readonly', false);
            }
        });
    });
    
    // Simpan semua (UPDATE)
    $('#btnSimpanSemua').on('click', function() {
        if (inventoryData.length === 0) {
            Swal.fire('Error', 'Belum ada data yang diinput!', 'error');
            return;
        }
        
        Swal.fire({
            title: 'Update Data?',
            html: `
                <p><strong>${inventoryData.length}</strong> segmen inventarisasi akan diperbarui</p>
                <p class="text-danger"><small><i class="fas fa-exclamation-triangle"></i> Data lama akan diganti dengan data baru</small></p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                updateToDatabase();
            }
        });
    });
    
    // Update to database via AJAX
    function updateToDatabase() {
        const payload = {
            _token: '{{ csrf_token() }}',
            _method: 'PUT',
            survey_setup: surveySetup,
            inventory_data: inventoryData
        };
        
        console.log('Payload yang dikirim:', payload);
        
        $.ajax({
            url: '{{ route("inventarisasi-jalan.update", $link->link_no) }}',
            type: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            dataType: 'json',
            beforeSend: function() {
                Swal.fire({
                    title: 'Memperbarui...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                console.log('Response sukses:', response);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message || 'Data inventarisasi berhasil diperbarui!',
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    window.location.href = '{{ route("inventarisasi-jalan.show", $link->link_no) }}';
                });
            },
            error: function(xhr, status, error) {
                console.error('Error response:', xhr);
                
                let errorMsg = 'Terjadi kesalahan saat memperbarui data';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                } else if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                    } catch (e) {
                        errorMsg = 'Server error: ' + xhr.statusText;
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memperbarui',
                    text: errorMsg,
                    confirmButtonColor: '#d33'
                });
            }
        });
    }
    
});
</script>
@endpush