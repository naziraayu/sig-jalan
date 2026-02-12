@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Tambah Data Inventarisasi Jalan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('inventarisasi-jalan.index') }}">Inventarisasi Jalan</a></div>
                <div class="breadcrumb-item active">Tambah Data</div>
            </div>
        </div>

        <div class="section-body">
            
            {{-- Alert jika belum pilih tahun --}}
            @if(!session('selected_year'))
                <div class="alert alert-warning alert-has-icon">
                    <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="alert-body">
                        <div class="alert-title">Perhatian!</div>
                        Silakan pilih tahun terlebih dahulu menggunakan filter tahun di pojok kanan atas.
                    </div>
                </div>
            @endif

            {{-- ==================== STEP 1: Setup Survey ==================== --}}
            <div id="step1" class="card" style="max-width: 900px; margin: 0 auto;">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-road"></i> ROAD SURVEY - Selection</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4">
                        <button type="button" class="btn btn-outline-secondary" disabled>
                            <i class="fas fa-chevron-left"></i> Sebelumnya
                        </button>
                        <h3 class="text-center mb-0">Inventarisasi Jalan</h3>
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
                            <select id="linkNo" class="form-control" required>
                                <option value="">-- Pilih Nomor Ruas --</option>
                                @foreach($ruasJalan as $ruas)
                                    <option value="{{ $ruas->link_no }}" 
                                            data-link-id="{{ $ruas->id }}"
                                            data-link-code="{{ $ruas->linkMaster?->link_code ?? $ruas->link_no }}"
                                            data-link-name="{{ $ruas->linkMaster?->link_name ?? 'Tidak ada nama' }}"
                                            data-link-length="{{ $ruas->link_length_official ?? 0 }}"
                                            data-province="{{ $ruas->province_code }}"
                                            data-kabupaten="{{ $ruas->kabupaten_code }}">
                                        {{ $ruas->linkMaster?->link_code ?? $ruas->link_no }} - {{ $ruas->linkMaster?->link_name ?? 'Tidak ada nama' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label font-weight-bold">Nama Ruas</label>
                        <div class="col-sm-8">
                            <input type="text" id="namaRuas" class="form-control bg-light" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label font-weight-bold">Panjang Ruas (km)</label>
                        <div class="col-sm-8">
                            <input type="text" id="panjangRuas" class="form-control bg-light" readonly>
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
                    <a href="{{ route('inventarisasi-jalan.index') }}" class="btn btn-secondary">
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
            <div class="card card-primary">
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
                    </table>
                    <button type="button" id="btnBack" class="btn btn-secondary btn-block">
                        <i class="fas fa-chevron-left"></i> Kembali
                    </button>
                    <button type="button" id="btnHapusSemua" class="btn btn-danger btn-block">
                        <i class="fas fa-trash"></i> Hapus semua
                    </button>
                </div>
            </div>
        </div>

        {{-- Kanan: Form Input --}}
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between">
                    <h4 class="mb-0">Inventarisasi Jalan</h4>
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
                                                <label class="font-weight-bold">Dari</label>
                                                <input type="number" id="inputDari" class="form-control" value="0" step="1">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Ke</label>
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
                                    <td colspan="9" class="text-center text-muted">Belum ada data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-right mt-3">
                        <button type="button" id="btnSimpanSemua" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Simpan Semua Data
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
    
    // ==================== STEP 1: SETUP ====================
    
    // Ketika ruas dipilih
    $('#linkNo').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const linkId = selectedOption.data('link-id');
        const linkName = selectedOption.data('link-name');
        const linkLength = selectedOption.data('link-length');
        
        if (linkId) {
            surveySetup.link_id = linkId;
            surveySetup.link_no = $(this).val();
            surveySetup.link_code = selectedOption.data('link-code');
            surveySetup.link_name = linkName;
            surveySetup.link_length = linkLength;
            surveySetup.province_code = selectedOption.data('province');
            surveySetup.kabupaten_code = selectedOption.data('kabupaten');
            
            $('#namaRuas').val(linkName);
            $('#panjangRuas').val(parseFloat(linkLength).toFixed(2));
            panjangRuasKm = parseFloat(linkLength);
        } else {
            surveySetup = {};
            $('#namaRuas').val('');
            $('#panjangRuas').val('');
            panjangRuasKm = 0;
        }
    });
    
    // Tombol Next
    $('#btnNext, #btnNextFooter').on('click', function() {
        // Validasi
        if (!$('#linkNo').val()) {
            Swal.fire('Error', 'Pilih Nomor Ruas terlebih dahulu!', 'error');
            return;
        }
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
        surveySetup.year = {{ session('selected_year') ?? date('Y') }};
        
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
            Swal.fire('Error', 'Total segmen sudah melebihi panjang ruas!', 'error');
            return;
        }
        
        // ✅ Validasi tidak boleh melebihi panjang ruas (dalam meter)
        const maxMeter = panjangRuasKm * 1000;
        if (ke > maxMeter) {
            Swal.fire('Error', `Nilai Ke tidak boleh melebihi panjang ruas (${maxMeter} m)!`, 'error');
            return;
        }
        
        if (!tipePerkerasan) {
            Swal.fire('Error', 'Tipe Perkerasan wajib diisi!', 'error');
            return;
        }
        
        // Validasi checkbox tak dapat dilalui
        if (takDapatDilalui && !alasanTakDapatDilalui) {
            Swal.fire('Error', 'Jika Tak Dapat Dilalui dicentang, alasan harus dipilih!', 'error');
            return;
        }
        
        // ✅ PERBAIKAN: Simpan dalam METER (BUKAN kilometer!)
        const dataItem = {
            chainage_from: dari,  // ✅ SUDAH DALAM METER, TIDAK PERLU DIBAGI 1000
            chainage_to: ke,      // ✅ SUDAH DALAM METER, TIDAK PERLU DIBAGI 1000
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
        
        // Tambah ke tabel
        renderTable();
        
        // ✅ Auto increment dengan logika cek sisa panjang ruas
        const nextDari = ke;
        let nextKe = ke + interval;
        
        // ✅ Jika nextKe melebihi panjang ruas, set ke maxMeter
        if (nextKe > maxMeter) {
            nextKe = maxMeter;
        }
        
        $('#inputDari').val(nextDari);
        $('#inputKe').val(nextKe);

        $('#inputTakDapatDilalui').prop('checked', false);
        $('#inputAlasanTakDapatDilalui').val('');

        // ✅ Cek apakah sudah mencapai akhir ruas
        if (nextKe >= maxMeter) {
            Swal.fire({
                icon: 'info',
                title: 'Segmen Terakhir',
                text: 'Anda telah mencapai akhir ruas jalan.',
                confirmButtonColor: '#28a745'
            });
            // Disable input atau langsung simpan
            $('#inputDari').prop('readonly', true);
            $('#inputKe').prop('readonly', true);
        } else {
            // Focus ke tombol Tambah supaya bisa langsung Enter
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
        
        if (inventoryData.length === 0) {
            $('#emptyRow').show();
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
                $('#inputDari').val(0);
                $('#inputKe').val(interval);
            }
        });
    });
    
    // Simpan semua
    $('#btnSimpanSemua').on('click', function() {
        if (inventoryData.length === 0) {
            Swal.fire('Error', 'Belum ada data yang diinput!', 'error');
            return;
        }
        
        Swal.fire({
            title: 'Simpan Data?',
            text: `${inventoryData.length} segmen inventarisasi akan disimpan`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                saveToDatabase();
            }
        });
    });
    
    // Save to database via AJAX
    function saveToDatabase() {
        const payload = {
            _token: '{{ csrf_token() }}',
            survey_setup: surveySetup,
            inventory_data: inventoryData
        };
        
        // ✅ Log untuk debugging (optional, bisa dihapus nanti)
        console.log('Payload yang dikirim:', payload);
        
        $.ajax({
            url: '{{ route("inventarisasi-jalan.store") }}',
            type: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            dataType: 'json', // ✅ Tambahkan ini
            beforeSend: function() {
                Swal.fire({
                    title: 'Menyimpan...',
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
                console.log('Response sukses:', response); // ✅ Log debugging
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message || 'Data inventarisasi berhasil disimpan!',
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    // ✅ Redirect ke index
                    window.location.href = '{{ route("inventarisasi-jalan.index") }}';
                });
            },
            error: function(xhr, status, error) {
                console.error('Error response:', xhr); // ✅ Log debugging
                
                let errorMsg = 'Terjadi kesalahan saat menyimpan data';
                
                // ✅ Parse error message dari server
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        // Jika ada validation errors
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
                    title: 'Gagal Menyimpan',
                    text: errorMsg,
                    confirmButtonColor: '#d33'
                });
            }
        });
    }
    
});
</script>
@endpush