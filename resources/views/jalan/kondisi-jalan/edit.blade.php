@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Data Kondisi Jalan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('kondisi-jalan.index') }}">Kondisi Jalan</a></div>
                <div class="breadcrumb-item active">Edit Data</div>
            </div>
        </div>

        <div class="section-body">
            
            {{-- CARD INFORMASI RUAS - READ ONLY --}}
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <h4><i class="fas fa-info-circle"></i> Informasi Ruas</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Ruas:</strong><br>
                            <span>{{ $link->linkMaster->link_code ?? $condition->link_no }} - {{ $link->linkMaster->link_name ?? 'Tidak ada nama' }}</span>
                        </div>
                        <div class="col-md-2">
                            <strong>Panjang:</strong><br>
                            <span>{{ number_format($link->link_length_official, 2) }} km</span>
                        </div>
                        <div class="col-md-2">
                            <strong>Chainage:</strong><br>
                            <span class="badge badge-info">{{ $condition->chainage_from }} - {{ $condition->chainage_to }} m</span>
                        </div>
                        <div class="col-md-2">
                            <strong>Tahun:</strong><br>
                            <span class="badge badge-primary">{{ $condition->year }}</span>
                        </div>
                        <div class="col-md-3 text-right">
                            <a href="{{ route('kondisi-jalan.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali ke Index
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM EDIT --}}
            <form id="formEditCondition" method="POST" action="{{ route('kondisi-jalan.update', [$condition->link_no, $condition->chainage_from, $condition->chainage_to, $condition->year]) }}">
                @csrf
                @method('PUT')
                
                {{-- Hidden input untuk simpan tipe data yang dipilih --}}
                <input type="hidden" name="data_type" id="selectedDataType" value="{{ $dataType }}">

                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0" id="headerTitle"><i class="fas fa-edit"></i> Edit Kondisi Jalan - {{ ucfirst($dataType) }}</h4>
                    </div>
                    <div class="card-body">
                        
                        {{-- METADATA SECTION --}}
                        <div class="card border mb-3">
                            <div class="card-header bg-light">
                                <strong><i class="fas fa-user-edit"></i> Metadata Survey</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Surveyor 1</label>
                                            <input type="text" name="survey_by" class="form-control" value="{{ old('survey_by', $condition->survey_by) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Surveyor 2 (Opsional)</label>
                                            <input type="text" name="survey_by2" class="form-control" value="{{ old('survey_by2', $condition->survey_by2) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Tanggal Survey</label>
                                            <input type="date" name="survey_date" class="form-control" value="{{ old('survey_date', $condition->survey_date) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CHAINAGE READ-ONLY --}}
                        <div class="card border mb-6">
                            <div class="card-header bg-light">
                                <strong><i class="fas fa-road"></i> Chainage (Read-Only)</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Dari (km)</label>
                                            <input type="text" class="form-control bg-light" 
                                                value="{{ number_format($condition->chainage_from / 1000, 3) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Ke (km)</label>
                                            <input type="text" class="form-control bg-light" 
                                                value="{{ number_format($condition->chainage_to / 1000, 3) }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TABS TIPE DATA --}}
                        <div class="btn-group mb-3 d-flex" role="group">
                            <button type="button" class="btn btn-outline-secondary flex-fill disabled">Kiri</button>
                            <button type="button" id="tabBeton" class="btn {{ $dataType == 'Beton' ? 'btn-primary' : 'btn-outline-primary' }} flex-fill">Beton</button>
                            <button type="button" id="tabBlok" class="btn {{ $dataType == 'Blok' ? 'btn-info' : 'btn-outline-info' }} flex-fill">Blok</button>
                            <button type="button" id="tabAspal" class="btn {{ $dataType == 'Aspal' ? 'btn-success' : 'btn-outline-success' }} flex-fill">Aspal</button>
                            <button type="button" id="tabNonAspal" class="btn {{ $dataType == 'Non Aspal' ? 'btn-warning' : 'btn-outline-warning' }} flex-fill">Non Aspal</button>
                            <button type="button" class="btn btn-outline-secondary flex-fill disabled">Kanan</button>
                        </div>

                        {{-- ========== FORM ASPAL ========== --}}
                        <div id="formAspal" style="display: {{ $dataType == 'Aspal' ? 'block' : 'none' }};">
                            
                            {{-- DATALIST PERCENTAGE ASPAL --}}
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

                            <datalist id="datalist-rusak-tepi">
                                <option value="0">Tidak Ada (0%)</option>
                                <option value="8">Ringan (0-30%)</option>
                                <option value="18">Berat (>30%)</option>
                            </datalist>

                            {{-- ROW 1: KERUSAKAN PERMUKAAN & RETAK-RETAK --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light">
                                            <strong>Kerusakan Permukaan</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Susunan</label>
                                                        <input type="text" name="roughness" class="form-control form-control-sm" list="datalist-susunan" value="{{ old('roughness', $condition->roughness) }}" placeholder="Pilih...">
                                                        <datalist id="datalist-susunan">
                                                            <option value="1">1 - Baik/rapat</option>
                                                            <option value="2">2 - Kasar</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Kegemukan (m²)</label>
                                                        <input type="text" name="bleeding_area" class="form-control form-control-sm" list="datalist-percentage-aspal" value="{{ old('bleeding_area', $condition->bleeding_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Agregat Lepas (m²)</label>
                                                        <input type="text" name="ravelling_area" class="form-control form-control-sm" list="datalist-percentage-aspal" value="{{ old('ravelling_area', $condition->ravelling_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Disintegrasi (m²)</label>
                                                        <input type="text" name="desintegration_area" class="form-control form-control-sm" list="datalist-percentage-aspal" value="{{ old('desintegration_area', $condition->desintegration_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Tambalan (m²)</label>
                                                        <input type="text" name="patching_area" class="form-control form-control-sm" list="datalist-percentage-aspal" value="{{ old('patching_area', $condition->patching_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light">
                                            <strong>Retak-Retak</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Jenis Retak</label>
                                                        <input type="text" name="crack_type" class="form-control form-control-sm" list="datalist-jenis-retak" value="{{ old('crack_type', $condition->crack_type) }}" placeholder="Pilih...">
                                                        <datalist id="datalist-jenis-retak">
                                                            <option value="1">1 - Tidak Ada</option>
                                                            <option value="2">2 - Tidak berhubungan</option>
                                                            <option value="3">3 - Saling berhubungan (luas)</option>
                                                            <option value="4">4 - Saling berhubungan (sempit)</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Rata² Lebar Retak</label>
                                                        <input type="text" name="crack_width" class="form-control form-control-sm" list="datalist-lebar-retak" value="{{ old('crack_width', $condition->crack_width) }}" placeholder="Pilih...">
                                                        <datalist id="datalist-lebar-retak">
                                                            <option value="1">1 - Tidak Ada</option>
                                                            <option value="2">2 - < 1 mm</option>
                                                            <option value="3">3 - 1 - 5 mm</option>
                                                            <option value="4">4 - > 5 mm</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Luas Retak Lain (m²)</label>
                                                        <input type="text" name="oth_crack_area" class="form-control form-control-sm" list="datalist-percentage-aspal" value="{{ old('oth_crack_area', $condition->oth_crack_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Retak Turun (m²)</label>
                                                        <input type="text" name="crack_dep_area" class="form-control form-control-sm" list="datalist-percentage-aspal" value="{{ old('crack_dep_area', $condition->crack_dep_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ROW 2: RUSAK TEPI & LUBANG --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light">
                                            <strong>Rusak Tepi</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Rusak Tepi Kiri (m²)</label>
                                                        <input type="text" name="edge_damage_area" class="form-control form-control-sm" list="datalist-rusak-tepi" value="{{ old('edge_damage_area', $condition->edge_damage_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Rusak Tepi Kanan (m²)</label>
                                                        <input type="text" name="edge_damage_area_r" class="form-control form-control-sm" list="datalist-rusak-tepi" value="{{ old('edge_damage_area_r', $condition->edge_damage_area_r) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light">
                                            <strong>Lubang</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>Jumlah</label>
                                                        <input type="text" name="pothole_count" class="form-control form-control-sm" list="datalist-jumlah-lubang" value="{{ old('pothole_count', $condition->pothole_count) }}" placeholder="Pilih...">
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
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>Ukuran</label>
                                                        <input type="text" name="pothole_size" class="form-control form-control-sm" list="datalist-ukuran-lubang" value="{{ old('pothole_size', $condition->pothole_size) }}" placeholder="Pilih...">
                                                        <datalist id="datalist-ukuran-lubang">
                                                            <option value="1">1 - Tidak Ada</option>
                                                            <option value="2">2 - Kecil-dangkal</option>
                                                            <option value="3">3 - Kecil-dalam</option>
                                                            <option value="4">4 - Besar-dangkal</option>
                                                            <option value="5">5 - Besar-dalam</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>Luas (m²)</label>
                                                        <input type="text" name="pothole_area" class="form-control form-control-sm" list="datalist-luas-lubang" value="{{ old('pothole_area', $condition->pothole_area) }}" placeholder="Pilih...">
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
                                </div>
                            </div>

                            {{-- ROW 3: ALUR --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <strong>Alur</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Alur (m²)</label>
                                                        <input type="text" name="rutting_area" class="form-control form-control-sm" list="datalist-percentage-aspal" value="{{ old('rutting_area', $condition->rutting_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Rata² Dlm Alur</label>
                                                        <input type="text" name="rutting_depth" class="form-control form-control-sm" list="datalist-dalam-alur" value="{{ old('rutting_depth', $condition->rutting_depth) }}" placeholder="Pilih...">
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
                            </div>
                        </div>

                        {{-- ========== FORM BLOK ========== --}}
                        <div id="formBlok" style="display: {{ $dataType == 'Blok' ? 'block' : 'none' }};">
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

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light">
                                            <strong>Kerusakan Utama</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Disintegrasi (m²)</label>
                                                        <input type="text" name="desintegration_area" class="form-control form-control-sm" list="datalist-percentage-beton" value="{{ old('desintegration_area', $condition->desintegration_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Retak Turun (m²)</label>
                                                        <input type="text" name="crack_dep_area" class="form-control form-control-sm" list="datalist-percentage-beton" value="{{ old('crack_dep_area', $condition->crack_dep_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Luas Lubang (m²)</label>
                                                        <input type="text" name="pothole_area" class="form-control form-control-sm" list="datalist-luas-lubang" value="{{ old('pothole_area', $condition->pothole_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Alur (m²)</label>
                                                        <input type="text" name="rutting_area" class="form-control form-control-sm" list="datalist-percentage-beton" value="{{ old('rutting_area', $condition->rutting_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light">
                                            <strong>Rusak Tepi</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Rusak Tepi Kiri (m²)</label>
                                                        <input type="text" name="edge_damage_area" class="form-control form-control-sm" list="datalist-rusak-tepi" value="{{ old('edge_damage_area', $condition->edge_damage_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Rusak Tepi Kanan (m²)</label>
                                                        <input type="text" name="edge_damage_area_r" class="form-control form-control-sm" list="datalist-rusak-tepi" value="{{ old('edge_damage_area_r', $condition->edge_damage_area_r) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========== FORM BETON ========== --}}
                        <div id="formBeton" style="display: {{ $dataType == 'Beton' ? 'block' : 'none' }};">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <strong>Kerusakan Beton</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Retak (m²)</label>
                                                        <input type="text" name="concrete_cracking_area" class="form-control form-control-sm" list="datalist-percentage-beton" value="{{ old('concrete_cracking_area', $condition->concrete_cracking_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Gompal (m²)</label>
                                                        <input type="text" name="concrete_spalling_area" class="form-control form-control-sm" list="datalist-percentage-beton" value="{{ old('concrete_spalling_area', $condition->concrete_spalling_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Retak Struktur (m²)</label>
                                                        <input type="text" name="concrete_structural_cracking_area" class="form-control form-control-sm" list="datalist-percentage-beton" value="{{ old('concrete_structural_cracking_area', $condition->concrete_structural_cracking_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Patahan/Penurunan (m²)</label>
                                                        <input type="text" name="concrete_blowouts_area" class="form-control form-control-sm" list="datalist-percentage-beton" value="{{ old('concrete_blowouts_area', $condition->concrete_blowouts_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Pumping (No)</label>
                                                        <input type="number" name="concrete_pumping_no" class="form-control form-control-sm" value="{{ old('concrete_pumping_no', $condition->concrete_pumping_no) }}" placeholder="0">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Pecah Sudut (No)</label>
                                                        <input type="number" name="concrete_corner_break_no" class="form-control form-control-sm" value="{{ old('concrete_corner_break_no', $condition->concrete_corner_break_no) }}" placeholder="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========== FORM NON ASPAL ========== --}}
                        <div id="formNonAspal" style="display: {{ $dataType == 'Non Aspal' ? 'block' : 'none' }};">
                            
                            {{-- ROW 1: KEMIRINGAN MELINTANG & KERUSAKAN PERMUKAAN --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light">
                                            <strong>Kemiringan Melintang</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>Kondisi</label>
                                                        <input type="text" name="should_cond_l" class="form-control form-control-sm" list="datalist-kondisi" value="{{ old('should_cond_l', $condition->should_cond_l) }}" placeholder="Pilih...">
                                                        <datalist id="datalist-kondisi">
                                                            <option value="4">4 - Cekung</option>
                                                            <option value="1">1 - > 5%</option>
                                                            <option value="2">2 - 3 - 5%</option>
                                                            <option value="3">3 - Rata</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>Kemiringan</label>
                                                        <input type="text" name="crossfall_shape" class="form-control form-control-sm" list="datalist-crossfall-shape" value="{{ old('crossfall_shape', $condition->crossfall_shape) }}" placeholder="Pilih...">
                                                        <datalist id="datalist-crossfall-shape">
                                                            <option value="1">1 - Tidak ada</option>
                                                            <option value="2">2 - Rata</option>
                                                            <option value="3">3 - Tidak Rata</option>
                                                            <option value="4">4 - Gundukan memanjang</option>
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>Luas (m²)</label>
                                                        <input type="text" name="crossfall_area" class="form-control form-control-sm" list="datalist-percentage-beton" value="{{ old('crossfall_area', $condition->crossfall_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light">
                                            <strong>Kerusakan Permukaan</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>Penurunan (m²)</label>
                                                        <input type="text" name="depressions_area" class="form-control form-control-sm" list="datalist-percentage-beton" value="{{ old('depressions_area', $condition->depressions_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>Erosi (m²)</label>
                                                        <input type="text" name="erosion_area" class="form-control form-control-sm" list="datalist-percentage-beton" value="{{ old('erosion_area', $condition->erosion_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>Bergelombang (m²)</label>
                                                        <input type="text" name="waviness_area" class="form-control form-control-sm" list="datalist-percentage-beton" value="{{ old('waviness_area', $condition->waviness_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ROW 2: KERIKIL --}}
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <strong>Kerikil</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Ukuran Kerikil</label>
                                                        <input type="text" name="gravel_size" class="form-control form-control-sm" list="datalist-ukuran-kerikil" value="{{ old('gravel_size', $condition->gravel_size) }}" placeholder="Pilih...">
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
                                                        <input type="text" name="gravel_thickness" class="form-control form-control-sm" list="datalist-tebal-kerikil" value="{{ old('gravel_thickness', $condition->gravel_thickness) }}" placeholder="Pilih...">
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
                                                        <input type="text" name="gravel_thickness_area" class="form-control form-control-sm" list="datalist-percentage-beton" value="{{ old('gravel_thickness_area', $condition->gravel_thickness_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Distribusi</label>
                                                        <input type="text" name="distribution" class="form-control form-control-sm" list="datalist-distribusi" value="{{ old('distribution', $condition->distribution) }}" placeholder="Pilih...">
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
                                </div>
                            </div>

                            {{-- ROW 3: LUBANG & ALUR --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light">
                                            <strong>Lubang</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>Jumlah</label>
                                                        <input type="number" name="pothole_count" class="form-control form-control-sm" value="{{ old('pothole_count', $condition->pothole_count) }}" placeholder="0">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>Ukuran</label>
                                                        <input type="text" name="pothole_size" class="form-control form-control-sm" list="datalist-ukuran-lubang" value="{{ old('pothole_size', $condition->pothole_size) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>Luas (m²)</label>
                                                        <input type="text" name="pothole_area" class="form-control form-control-sm" list="datalist-luas-lubang" value="{{ old('pothole_area', $condition->pothole_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light">
                                            <strong>Alur</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Alur (m²)</label>
                                                        <input type="text" name="rutting_area" class="form-control form-control-sm" list="datalist-percentage-beton" value="{{ old('rutting_area', $condition->rutting_area) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>Rata² Dlm Alur</label>
                                                        <input type="text" name="rutting_depth" class="form-control form-control-sm" list="datalist-dalam-alur" value="{{ old('rutting_depth', $condition->rutting_depth) }}" placeholder="Pilih...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ route('kondisi-jalan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    
    function switchDataType(type) {
        $('#formAspal, #formBlok, #formBeton, #formNonAspal').hide();
        
        $('#tabAspal, #tabBlok, #tabBeton, #tabNonAspal')
            .removeClass('btn-success btn-info btn-primary btn-warning')
            .addClass('btn-outline-secondary');
        
        if (type === 'Aspal') {
            $('#formAspal').show();
            $('#tabAspal').removeClass('btn-outline-secondary').addClass('btn-success');
        } else if (type === 'Blok') {
            $('#formBlok').show();
            $('#tabBlok').removeClass('btn-outline-secondary').addClass('btn-info');
        } else if (type === 'Beton') {
            $('#formBeton').show();
            $('#tabBeton').removeClass('btn-outline-secondary').addClass('btn-primary');
        } else if (type === 'Non Aspal') {
            $('#formNonAspal').show();
            $('#tabNonAspal').removeClass('btn-outline-secondary').addClass('btn-warning');
        }
        
        $('#selectedDataType').val(type);
        $('#headerTitle').html('<i class="fas fa-edit"></i> Edit Kondisi Jalan - ' + type);
        
        console.log('✅ Switched to:', type);
    }
    
    $('#tabAspal').on('click', function() {
        switchDataType('Aspal');
    });
    
    $('#tabBlok').on('click', function() {
        switchDataType('Blok');
    });
    
    $('#tabBeton').on('click', function() {
        switchDataType('Beton');
    });
    
    $('#tabNonAspal').on('click', function() {
        switchDataType('Non Aspal');
    });
    
    $('#formEditCondition').on('submit', function(e) {
        e.preventDefault();
        
        var selectedType = $('#selectedDataType').val();
        
        var pavementNames = {
            'Aspal': 'Aspal (Asphalt)',
            'Blok': 'Blok (Block)',
            'Beton': 'Beton (Concrete)',
            'Non Aspal': 'Non Aspal (Unpaved)',
            'Tak Dapat Dilalui': 'Tak Dapat Dilalui (Impassable)'
        };
        
        var displayName = pavementNames[selectedType] || selectedType;
        
        console.log('Submitting with data_type:', selectedType);
        
        Swal.fire({
            title: 'Simpan Perubahan?',
            html: `
                <p>Data kondisi jalan tipe <strong>${displayName}</strong> akan diupdate.</p>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> SDI akan dihitung ulang otomatis oleh sistem.
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
    
    console.log('✅ Edit Form Initialized');
    console.log('Current data_type:', $('#selectedDataType').val());
});
</script>
@endpush