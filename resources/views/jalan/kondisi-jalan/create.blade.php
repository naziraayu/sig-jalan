@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Tambah Data Kondisi Jalan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('kondisi-jalan.index') }}">Kondisi Jalan</a></div>
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
                        <h3 class="text-center mb-0">Kondisi Jalan</h3>
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
                            <small class="text-muted">Data ruas dari tahun {{ $referenceYear }} (Reference Year)</small>
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
                        <label class="col-sm-4 col-form-label font-weight-bold">Survei oleh 2</label>
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
                        <label class="col-sm-4 col-form-label font-weight-bold">
                            Pengumpulan Data <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-8">
                            <div class="btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-success mr-2">
                                    <input type="radio" name="data_type" value="Aspal" checked> Aspal
                                </label>
                                <label class="btn btn-outline-info mr-2">
                                    <input type="radio" name="data_type" value="Blok"> Blok
                                </label>
                                <label class="btn btn-outline-primary mr-2">
                                    <input type="radio" name="data_type" value="Beton"> Beton
                                </label>
                                <label class="btn btn-outline-warning mr-2">
                                    <input type="radio" name="data_type" value="Non Aspal"> Non Aspal
                                </label>
                                <label class="btn btn-outline-danger">
                                    <input type="radio" name="data_type" value="Tak Dapat Dilalui"> Tak Dapat Dilalui
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('kondisi-jalan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="button" id="btnNextFooter" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> Selanjutnya
                    </button>
                </div>
            </div>

            {{-- ==================== STEP 2: INPUT DETAIL ==================== --}}
            <div id="step2" style="display: none;">
                
                {{-- CARD INFORMASI RUAS - DITARUH DI ATAS --}}
                <div class="card card-primary mb-3">
                    <div class="card-header">
                        <h4><i class="fas fa-info-circle"></i> Informasi Ruas</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <strong>Ruas:</strong><br>
                                <span id="infoRuas">-</span>
                            </div>
                            <div class="col-md-2">
                                <strong>Panjang:</strong><br>
                                <span id="infoPanjang">-</span>
                            </div>
                            <div class="col-md-2">
                                <strong>Arah:</strong><br>
                                <span id="infoArah">-</span>
                            </div>
                            <div class="col-md-2">
                                <strong>Interval (m):</strong><br>
                                <span id="infoInterval">-</span>
                            </div>
                            <div class="col-md-2">
                                <strong>Tipe Data:</strong><br>
                                <span id="infoTipeData">-</span>
                            </div>
                            <div class="col-md-2 text-right">
                                <button type="button" id="btnBack" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-chevron-left"></i> Kembali
                                </button>
                                <button type="button" id="btnHapusSemua" class="btn btn-danger btn-sm mt-1">
                                    <i class="fas fa-trash"></i> Hapus Semua
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD INPUT KONDISI JALAN - DITARUH DI BAWAH --}}
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between">
                        <h4 class="mb-0">Kondisi Jalan - <span id="headerTipeData">Aspal</span></h4>
                    </div>
                    <div class="card-body">
                        {{-- Tabs Kiri/Beton/Blok/Aspal/Non Aspal/Kanan --}}
                        <div class="btn-group mb-3 d-flex" role="group">
                            <button type="button" class="btn btn-outline-secondary flex-fill disabled">Kiri</button>
                            <button type="button" id="tabBeton" class="btn btn-outline-primary flex-fill">Beton</button>
                            <button type="button" id="tabBlok" class="btn btn-outline-info flex-fill">Blok</button>
                            <button type="button" id="tabAspal" class="btn btn-success flex-fill">Aspal</button>
                            <button type="button" id="tabNonAspal" class="btn btn-outline-warning flex-fill">Non Aspal</button>
                            <button type="button" class="btn btn-outline-secondary flex-fill disabled">Kanan</button>
                        </div>

                        {{-- Form Input Data --}}
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Data Masukan</h5>
                            </div>
                            <div class="card-body">
                                {{-- Chainage (Selalu ada) --}}
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Dari (km)</label>
                                            <input type="number" id="inputDari" class="form-control" value="0" step="0.001">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Ke (km)</label>
                                            <input type="number" id="inputKe" class="form-control" step="0.001">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- ========== FORM ASPAL ========== --}}
                                <div id="formAspal" class="form-kondisi">
                                    
                                    {{-- CARD 1: KERUSAKAN PERMUKAAN --}}
                                    <div class="card border mb-3">
                                        <div class="card-header bg-light">
                                            <strong>Kerusakan Permukaan</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Susunan</label>
                                                        <input type="text" class="form-control" list="datalist-susunan" data-field="roughness" placeholder="Ketik atau pilih...">
                                                        <datalist id="datalist-susunan">
                                                            <option value="1">1 - Baik/rapat</option>
                                                            <option value="2">2 - Kasar</option>
                                                        </datalist>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Kegemukan (m²)</label>
                                                        <input type="text" class="form-control dropdown-with-value" list="datalist-percentage-aspal" data-field="bleeding_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Agregat Lepas (m²)</label>
                                                        <input type="text" class="form-control dropdown-with-value" list="datalist-percentage-aspal" data-field="ravelling_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Disintegrasi (m²)</label>
                                                        <input type="text" class="form-control dropdown-with-value" list="datalist-percentage-aspal" data-field="desintegration_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Tambalan (m²)</label>
                                                        <input type="text" class="form-control dropdown-with-value" list="datalist-percentage-aspal" data-field="patching_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- DATALIST PERCENTAGE ASPAL (Reusable) - Tambah 0 = 0 --}}
                                    <datalist id="datalist-percentage-aspal">
                                        <option value="0">0</option>
                                        <option value="18">0 - 5%</option>
                                        <option value="52">5 - 10%</option>
                                        <option value="105">10 - 20%</option>
                                        <option value="175">20 - 30%</option>
                                        <option value="245">30 - 40%</option>
                                        <option value="315">40 - 50%</option>
                                        <option value="525">50% ></option>
                                    </datalist>

                                    {{-- CARD 2: RETAK-RETAK --}}
                                    <div class="card border mb-3">
                                        <div class="card-header bg-light">
                                            <strong>Retak-Retak</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Jenis Retak</label>
                                                        <input type="text" class="form-control" list="datalist-jenis-retak" data-field="crack_type" placeholder="Ketik atau pilih...">
                                                        <datalist id="datalist-jenis-retak">
                                                            <option value="1">1 - Tidak Ada</option>
                                                            <option value="2">2 - Tidak berhubungan</option>
                                                            <option value="3">3 - Saling berhubungan (berbidang luas)</option>
                                                            <option value="4">4 - Saling berhubungan (berbidang sempit)</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Rata² Lebar Retak</label>
                                                        <input type="text" class="form-control" list="datalist-lebar-retak" data-field="crack_width" placeholder="Ketik atau pilih...">
                                                        <datalist id="datalist-lebar-retak">
                                                            <option value="1">1 - Tidak Ada</option>
                                                            <option value="2">2 - < 1 mm</option>
                                                            <option value="3">3 - 1 - 5 mm</option>
                                                            <option value="4">4 - > 5 mm</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Luas Retak Lain (m²)</label>
                                                        <input type="text" class="form-control dropdown-with-value" list="datalist-percentage-aspal" data-field="oth_crack_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Retak Turun (m²)</label>
                                                        <input type="text" class="form-control dropdown-with-value" list="datalist-percentage-aspal" data-field="crack_dep_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- CARD 3: RUSAK TEPI --}}
                                    <div class="card border mb-3">
                                        <div class="card-header bg-light">
                                            <strong>Rusak Tepi</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Rusak Tepi Kiri (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-rusak-tepi" data-field="edge_damage_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Rusak Tepi Kanan (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-rusak-tepi" data-field="edge_damage_area_r" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- DATALIST RUSAK TEPI --}}
                                    <datalist id="datalist-rusak-tepi">
                                        <option value="0">Tidak Ada (0%)</option>
                                        <option value="8">Ringan (0-30%)</option>
                                        <option value="18">Berat (>30%)</option>
                                    </datalist>

                                    {{-- CARD 4: LUBANG --}}
                                    <div class="card border mb-3">
                                        <div class="card-header bg-light">
                                            <strong>Lubang</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Jumlah Lubang</label>
                                                        <input type="text" class="form-control" list="datalist-jumlah-lubang" data-field="pothole_count" placeholder="Ketik atau pilih...">
                                                        <datalist id="datalist-jumlah-lubang">
                                                            <option value="0">0</option>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                            <option value="6">6</option>
                                                            <option value="7">7</option>
                                                            <option value="8">>7</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Ukuran Lubang</label>
                                                        <input type="text" class="form-control" list="datalist-ukuran-lubang" data-field="pothole_size" placeholder="Ketik atau pilih...">
                                                        <datalist id="datalist-ukuran-lubang">
                                                            <option value="1">1 - Tidak Ada</option>
                                                            <option value="2">2 - Kecil-dangkal</option>
                                                            <option value="3">3 - Kecil-dalam</option>
                                                            <option value="4">4 - Besar-dangkal</option>
                                                            <option value="5">5 - Besar-dalam</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Luas Lubang (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-luas-lubang" data-field="pothole_area" placeholder="Ketik atau pilih...">
                                                        <datalist id="datalist-luas-lubang">
                                                            <option value="0">0</option>
                                                            <option value="10">0 - 3%</option>
                                                            <option value="28">3 - 5%</option>
                                                            <option value="52">5 - 10%</option>
                                                            <option value="105">10 - 20%</option>
                                                            <option value="175">20 - 30%</option>
                                                            <option value="245">30 - 40%</option>
                                                            <option value="315">40 - 50%</option>
                                                            <option value="525">50% ></option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- CARD 5: ALUR --}}
                                    <div class="card border mb-3">
                                        <div class="card-header bg-light">
                                            <strong>Alur</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Alur (m²)</label>
                                                        <input type="text" class="form-control dropdown-with-value" list="datalist-percentage-aspal" data-field="rutting_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Rata² Dlm Alur</label>
                                                        <input type="text" class="form-control" list="datalist-dalam-alur" data-field="rutting_depth" placeholder="Ketik atau pilih...">
                                                        <datalist id="datalist-dalam-alur">
                                                            <option value="1">1 - Tidak Ada</option>
                                                            <option value="2">2 - < 1 cm</option>
                                                            <option value="3">3 - 1 - 3 cm</option>
                                                            <option value="4">4 - > 3 cm</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                {{-- ========== FORM BLOK ========== --}}
                                <div id="formBlok" class="form-kondisi" style="display: none;">
                                    <div class="card border mb-3">
                                        <div class="card-header bg-light">
                                            <strong>Kerusakan Blok</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Disintegrasi (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-percentage-beton" data-field="desintegration_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Retak Turun (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-percentage-beton" data-field="crack_dep_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Rusak Tepi Kiri (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-rusak-tepi" data-field="edge_damage_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Luas Lubang (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-luas-lubang" data-field="pothole_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Alur (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-percentage-beton" data-field="rutting_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Rusak Tepi Kanan (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-rusak-tepi" data-field="edge_damage_area_r" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ========== FORM BETON ========== --}}
                                <div id="formBeton" class="form-kondisi" style="display: none;">
                                    
                                    {{-- CARD: KERUSAKAN BETON --}}
                                    <div class="card border mb-3">
                                        <div class="card-header bg-light">
                                            <strong>Kerusakan Beton</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Retak (m²)</label>
                                                        <input type="text" class="form-control dropdown-with-value" list="datalist-percentage-beton" data-field="concrete_cracking_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Gompal (m²)</label>
                                                        <input type="text" class="form-control dropdown-with-value" list="datalist-percentage-beton" data-field="concrete_spalling_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Retak Struktur (m²)</label>
                                                        <input type="text" class="form-control dropdown-with-value" list="datalist-percentage-beton" data-field="concrete_structural_cracking_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Patahan/Penurunan (m²)</label>
                                                        <input type="text" class="form-control dropdown-with-value" list="datalist-percentage-beton" data-field="concrete_blowouts_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Pumping (No)</label>
                                                        <input type="number" class="form-control" data-field="concrete_pumping_no" placeholder="Masukkan jumlah...">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Pecah Sudut (No)</label>
                                                        <input type="number" class="form-control" data-field="concrete_corner_break_no" placeholder="Masukkan jumlah...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- DATALIST PERCENTAGE UNTUK BETON --}}
                                    <datalist id="datalist-percentage-beton">
                                        <option value="0">0</option>
                                        <option value="18">0 - 5%</option>
                                        <option value="52">5 - 10%</option>
                                        <option value="105">10 - 20%</option>
                                        <option value="175">20 - 30%</option>
                                        <option value="245">30 - 40%</option>
                                        <option value="315">40 - 50%</option>
                                        <option value="525">50% ></option>
                                    </datalist>

                                </div>

                                {{-- ========== FORM NON ASPAL ========== --}}
                                <div id="formNonAspal" class="form-kondisi" style="display: none;">
                                    
                                    {{-- CARD 1: KEMIRINGAN MELINTANG --}}
                                    <div class="card border mb-3">
                                        <div class="card-header bg-light">
                                            <strong>Kemiringan Melintang</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Kondisi</label>
                                                        <input type="text" class="form-control" list="datalist-kondisi" data-field="should_cond_l" placeholder="Ketik atau pilih...">
                                                        <datalist id="datalist-kondisi">
                                                            <option value="4">4 - Cekung</option>
                                                            <option value="1">1 - > 5%</option>
                                                            <option value="2">2 - 3 - 5%</option>
                                                            <option value="3">3 - Rata</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Kemiringan Melintang</label>
                                                        <input type="text" class="form-control" list="datalist-crossfall-shape" data-field="crossfall_shape" placeholder="Ketik atau pilih...">
                                                        <datalist id="datalist-crossfall-shape">
                                                            <option value="1">1 - Tidak ada</option>
                                                            <option value="2">2 - Rata</option>
                                                            <option value="3">3 - Tidak Rata</option>
                                                            <option value="4">4 - Gundukan memanjang</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Luas (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-percentage-beton" data-field="crossfall_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- CARD 2: KERUSAKAN PERMUKAAN --}}
                                    <div class="card border mb-3">
                                        <div class="card-header bg-light">
                                            <strong>Kerusakan Permukaan</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Penurunan (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-percentage-beton" data-field="depressions_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Erosi (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-percentage-beton" data-field="erosion_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Bergelombang (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-percentage-beton" data-field="waviness_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- CARD 3: KERIKIL --}}
                                    <div class="card border mb-3">
                                        <div class="card-header bg-light">
                                            <strong>Kerikil</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Ukuran Terbanyak Kerikil</label>
                                                        <input type="text" class="form-control" list="datalist-ukuran-kerikil" data-field="gravel_size" placeholder="Ketik atau pilih...">
                                                        <datalist id="datalist-ukuran-kerikil">
                                                            <option value="1">1 - Tidak ada</option>
                                                            <option value="2">2 - < 5 cm</option>
                                                            <option value="3">3 - 5 - 10 cm</option>
                                                            <option value="4">4 - 10 - 20 cm</option>
                                                            <option value="5">5 - > 20 cm</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Tebal Kerikil</label>
                                                        <input type="text" class="form-control" list="datalist-tebal-kerikil" data-field="gravel_thickness" placeholder="Ketik atau pilih...">
                                                        <datalist id="datalist-tebal-kerikil">
                                                            <option value="1">1 - Tidak ada</option>
                                                            <option value="2">2 - < 5 cm</option>
                                                            <option value="3">3 - 5 - 10 cm</option>
                                                            <option value="4">4 - 10 - 20 cm</option>
                                                            <option value="5">5 - > 20 cm</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Luas Kerikil (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-percentage-beton" data-field="gravel_thickness_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Distribusi</label>
                                                        <input type="text" class="form-control" list="datalist-distribusi" data-field="distribution" placeholder="Ketik atau pilih...">
                                                        <datalist id="datalist-distribusi">
                                                            <option value="1">1 - Tidak ada</option>
                                                            <option value="2">2 - Rata</option>
                                                            <option value="3">3 - Tidak Rata</option>
                                                            <option value="4">4 - Gundukan memanjang</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- CARD 4: LUBANG --}}
                                    <div class="card border mb-3">
                                        <div class="card-header bg-light">
                                            <strong>Lubang</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Jumlah Lubang</label>
                                                        <input type="number" class="form-control" data-field="pothole_count" placeholder="Masukkan jumlah...">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Ukuran Lubang</label>
                                                        <input type="text" class="form-control" list="datalist-ukuran-lubang" data-field="pothole_size" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Luas Lubang (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-luas-lubang" data-field="pothole_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- CARD 5: ALUR --}}
                                    <div class="card border mb-3">
                                        <div class="card-header bg-light">
                                            <strong>Alur</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Alur (m²)</label>
                                                        <input type="text" class="form-control" list="datalist-percentage-beton" data-field="rutting_area" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Rata² Dlm Alur</label>
                                                        <input type="text" class="form-control" list="datalist-dalam-alur" data-field="rutting_depth" placeholder="Ketik atau pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                {{-- ========== FORM TAK DAPAT DILALUI ========== --}}
                                <div id="formTakDapatDilalui" class="form-kondisi" style="display: none;">
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        Segmen ini ditandai sebagai <strong>Tak Dapat Dilalui</strong>. 
                                        Klik tombol "Tambah" untuk menyimpan.
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
                            <table id="tabelPreview" class="table table-bordered table-striped table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Dari (km)</th>
                                        <th>Ke (km)</th>
                                        <th>Tipe</th>
                                        <th>Data Kerusakan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyPreview">
                                    <tr id="emptyRow">
                                        <td colspan="6" class="text-center text-muted">Belum ada data</td>
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
    </section>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    
    // ==================== VARIABLES ====================
    let surveySetup = {};
    let conditionData = [];
    let panjangRuasKm = 0;
    let interval = 100;
    let dataType = 'Aspal';
    let availableChainages = [];
    
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
            
            // Load chainage dari inventory
            loadChainages($(this).val());
        } else {
            surveySetup = {};
            $('#namaRuas').val('');
            $('#panjangRuas').val('');
            panjangRuasKm = 0;
            availableChainages = [];
        }
    });
    
    // Load chainage dari inventory
    function loadChainages(linkNo) {
        $.ajax({
            url: '{{ route("kondisi-jalan.getChainageByRuas") }}',
            type: 'GET',
            data: {
                link_no: linkNo,
                year: {{ $selectedYear }}
            },
            success: function(response) {
                if (response.success) {
                    availableChainages = response.data;
                    console.log('Available chainages:', availableChainages);
                } else {
                    Swal.fire('Peringatan', response.message, 'warning');
                    availableChainages = [];
                }
            },
            error: function(xhr) {
                console.error('Error loading chainages:', xhr);
                Swal.fire('Error', 'Gagal memuat data chainage', 'error');
                availableChainages = [];
            }
        });
    }
    
    // Pilih tipe data
    $('input[name="data_type"]').on('change', function() {
        dataType = $(this).val();
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
        
        if (availableChainages.length === 0) {
            Swal.fire('Error', 'Ruas ini belum memiliki data inventarisasi. Silakan buat inventarisasi terlebih dahulu.', 'error');
            return;
        }
        
        // Simpan data setup
        surveySetup.surveyor_name = $('#surveyorName').val();
        surveySetup.surveyor_name_2 = $('#surveyorName2').val();
        surveySetup.survey_date = $('#surveyDate').val();
        surveySetup.direction = $('#direction').val();
        surveySetup.interval = parseInt($('#interval').val());
        surveySetup.data_collection_type = dataType;
        surveySetup.year = {{ $selectedYear }};
        surveySetup.reference_year = {{ $referenceYear }};
        
        interval = surveySetup.interval;
        
        // Tampilkan step 2
        $('#step1').hide();
        $('#step2').show();
        
        // Update info ruas
        $('#infoRuas').html(`<strong>${surveySetup.link_code}</strong><br><small>${surveySetup.link_name}</small>`);
        $('#infoPanjang').html(`<strong>${parseFloat(surveySetup.link_length).toFixed(2)} km</strong>`);
        $('#infoArah').text(surveySetup.direction);
        $('#infoInterval').text(surveySetup.interval);
        $('#infoTipeData').html(`<span class="badge badge-info">${dataType}</span>`);
        $('#headerTipeData').text(dataType);
        
        // Show form sesuai tipe data
        showFormByType(dataType);
        
        // Set nilai awal Ke
        if (availableChainages.length > 0) {
            const firstChainage = availableChainages[0];
            $('#inputDari').val(firstChainage.chainage_from);
            $('#inputKe').val(firstChainage.chainage_to);
        }
    });
    
    // Tombol Back
    $('#btnBack').on('click', function() {
        $('#step2').hide();
        $('#step1').show();
    });
    
    // ==================== STEP 2: TABS & FORMS ====================
    
    // Tab click handlers
    $('#tabAspal').on('click', function() {
        showFormByType('Aspal');
    });
    
    $('#tabBlok').on('click', function() {
        showFormByType('Blok');
    });
    
    $('#tabBeton').on('click', function() {
        showFormByType('Beton');
    });
    
    $('#tabNonAspal').on('click', function() {
        showFormByType('Non Aspal');
    });
    
    function showFormByType(type) {
        // Hide all forms
        $('.form-kondisi').hide();
        
        // Remove active class from all tabs
        $('.btn-group button').removeClass('btn-success btn-primary btn-info btn-warning')
            .addClass('btn-outline-secondary');
        
        // Show selected form and activate tab
        switch(type) {
            case 'Aspal':
                $('#formAspal').show();
                $('#tabAspal').removeClass('btn-outline-secondary').addClass('btn-success');
                break;
            case 'Blok':
                $('#formBlok').show();
                $('#tabBlok').removeClass('btn-outline-secondary').addClass('btn-info');
                break;
            case 'Beton':
                $('#formBeton').show();
                $('#tabBeton').removeClass('btn-outline-secondary').addClass('btn-primary');
                break;
            case 'Non Aspal':
                $('#formNonAspal').show();
                $('#tabNonAspal').removeClass('btn-outline-secondary').addClass('btn-warning');
                break;
            case 'Tak Dapat Dilalui':
                $('#formTakDapatDilalui').show();
                break;
        }
        
        dataType = type;
        $('#headerTipeData').text(type);
        $('#infoTipeData').html(`<span class="badge badge-info">${type}</span>`);
    }
    
    // ==================== INPUT & VALIDATION ====================
    
    // Tombol Tambah
    $('#btnTambah').on('click', function() {
        const dari = parseFloat($('#inputDari').val());
        const ke = parseFloat($('#inputKe').val());
        
        // Validasi
        if (isNaN(dari) || isNaN(ke)) {
            Swal.fire('Error', 'Nilai Dari dan Ke harus diisi!', 'error');
            return;
        }
        if (ke <= dari) {
            Swal.fire('Error', 'Nilai Ke harus lebih besar dari Dari!', 'error');
            return;
        }
        
        // Validasi: chainage harus ada di inventory
        const validChainage = availableChainages.find(ch => 
            ch.chainage_from <= dari && ch.chainage_to >= ke
        );
        
        if (!validChainage) {
            Swal.fire('Error', 'Chainage tidak valid! Pastikan dari-ke sesuai dengan data inventarisasi.', 'error');
            return;
        }
        
        // Collect data dari form yang aktif
        const dataItem = {
            chainage_from: dari,
            chainage_to: ke,
            data_type: dataType
        };
        
        // Collect field values from inputs and selects
        $('.form-kondisi:visible input[data-field]').each(function() {
            const field = $(this).data('field');
            const value = $(this).val();
            if (value !== '' && value !== null) {
                dataItem[field] = value;
            }
        });
        
        conditionData.push(dataItem);
        
        // Tambah ke tabel
        renderTable();
        
        // Auto increment chainage ke segmen berikutnya
        const nextSegment = availableChainages.find(ch => ch.chainage_from >= ke);
        if (nextSegment) {
            $('#inputDari').val(nextSegment.chainage_from);
            $('#inputKe').val(nextSegment.chainage_to);
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Segmen Terakhir',
                text: 'Anda telah mencapai akhir segmen inventarisasi.',
                confirmButtonColor: '#28a745'
            });
        }
        
        // Clear form inputs
        $('.form-kondisi:visible input[data-field]').val('');
    });
    
    // Render tabel
    function renderTable() {
        $('#emptyRow').hide();
        $('#tbodyPreview').empty();
        
        conditionData.forEach((item, index) => {
            const dataInfo = getDataInfo(item);
            
            const row = `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td class="text-center">${item.chainage_from.toFixed(3)}</td>
                    <td class="text-center">${item.chainage_to.toFixed(3)}</td>
                    <td class="text-center"><span class="badge badge-info">${item.data_type}</span></td>
                    <td><small>${dataInfo}</small></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" onclick="hapusData(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#tbodyPreview').append(row);
        });
        
        if (conditionData.length === 0) {
            $('#emptyRow').show();
        }
    }
    
    function getDataInfo(item) {
        const fields = [];
        for (const [key, value] of Object.entries(item)) {
            if (key !== 'chainage_from' && key !== 'chainage_to' && key !== 'data_type' && value) {
                fields.push(`${key}: ${value}`);
            }
        }
        return fields.length > 0 ? fields.join(', ') : '-';
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
                conditionData.splice(index, 1);
                renderTable();
            }
        });
    };
    
    // Hapus semua
    $('#btnHapusSemua').on('click', function() {
        if (conditionData.length === 0) {
            Swal.fire('Info', 'Belum ada data yang diinput', 'info');
            return;
        }
        
        Swal.fire({
            title: 'Hapus Semua Data?',
            text: `${conditionData.length} data akan dihapus dari daftar`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus Semua',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                conditionData = [];
                renderTable();
                
                // Reset ke chainage pertama
                if (availableChainages.length > 0) {
                    const firstChainage = availableChainages[0];
                    $('#inputDari').val(firstChainage.chainage_from);
                    $('#inputKe').val(firstChainage.chainage_to);
                }
            }
        });
    });
    
    // Simpan semua
    $('#btnSimpanSemua').on('click', function() {
        if (conditionData.length === 0) {
            Swal.fire('Error', 'Belum ada data yang diinput!', 'error');
            return;
        }
        
        Swal.fire({
            title: 'Simpan Data?',
            text: `${conditionData.length} segmen kondisi jalan akan disimpan`,
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
            condition_data: conditionData
        };
        
        console.log('Payload yang dikirim:', payload);
        
        $.ajax({
            url: '{{ route("kondisi-jalan.store") }}',
            type: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            dataType: 'json',
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
                console.log('Response sukses:', response);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message || 'Data kondisi jalan berhasil disimpan!',
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    window.location.href = '{{ route("kondisi-jalan.index") }}';
                });
            },
            error: function(xhr, status, error) {
                console.error('Error response:', xhr);
                
                let errorMsg = 'Terjadi kesalahan saat menyimpan data';
                
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