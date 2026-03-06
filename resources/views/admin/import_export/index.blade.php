@extends('layouts.template')

@section('content')

<style>
  /* ── Progress Modal ── */
  .progress-modal-icon {
    width: 68px; height: 68px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px; font-size: 28px; color: #fff;
  }
  .progress-modal-icon.type-export   { background: linear-gradient(135deg, #28a745, #20c997); }
  .progress-modal-icon.type-import   { background: linear-gradient(135deg, #6777ef, #a855f7); }
  .progress-modal-icon.type-template { background: linear-gradient(135deg, #fd7e14, #ffc107); }

  .ie-progress-wrap {
    background: #edf0f7; border-radius: 50px;
    height: 10px; overflow: hidden; margin: 14px 0 6px;
  }
  .ie-progress-fill {
    height: 100%; border-radius: 50px; width: 0%;
    transition: width 0.35s ease;
    background: linear-gradient(90deg, #6777ef 0%, #a855f7 50%, #6777ef 100%);
    background-size: 200% 100%;
    animation: shimmer 1.6s linear infinite;
  }
  .ie-progress-fill.export-bar   { background: linear-gradient(90deg, #28a745 0%, #20c997 50%, #28a745 100%); background-size: 200% 100%; }
  .ie-progress-fill.template-bar { background: linear-gradient(90deg, #fd7e14 0%, #ffc107 50%, #fd7e14 100%); background-size: 200% 100%; }

  @keyframes shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  .ie-progress-meta {
    display: flex; justify-content: space-between;
    align-items: center; margin-bottom: 14px;
  }
  .ie-progress-meta .step-label { font-size: 12px; color: #6c757d; }
  .ie-progress-meta .step-pct   { font-size: 13px; font-weight: 700; color: #6777ef; }
  .ie-progress-meta .step-pct.export-pct   { color: #28a745; }
  .ie-progress-meta .step-pct.template-pct { color: #fd7e14; }

  .ie-steps { list-style: none; padding: 0; margin: 0; }
  .ie-steps li {
    display: flex; align-items: center; gap: 10px;
    padding: 7px 0; font-size: 13px; color: #ced4da;
    border-bottom: 1px solid #f0f2f8; transition: color 0.3s;
  }
  .ie-steps li:last-child { border-bottom: none; }
  .ie-steps li.active { color: #343a40; font-weight: 600; }
  .ie-steps li.done   { color: #28a745; }

  .ie-steps .sdot {
    width: 24px; height: 24px; border-radius: 50%;
    border: 2px solid #dee2e6;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; flex-shrink: 0; color: #ced4da; transition: all 0.3s;
  }
  .ie-steps li.active .sdot {
    border-color: #6777ef; background: #6777ef; color: #fff;
    animation: dotPulse 1.1s ease-in-out infinite alternate;
  }
  .ie-steps li.active .sdot.export-dot   { border-color: #28a745; background: #28a745; }
  .ie-steps li.active .sdot.template-dot { border-color: #fd7e14; background: #fd7e14; }
  .ie-steps li.done .sdot { border-color: #28a745; background: #28a745; color: #fff; }

  @keyframes dotPulse {
    from { box-shadow: 0 0 0 0 rgba(103,119,239,0.5); }
    to   { box-shadow: 0 0 0 7px rgba(103,119,239,0); }
  }

  #loadingModal .modal-content {
    border: none; border-radius: 18px;
    box-shadow: 0 24px 70px rgba(0,0,0,0.16); overflow: hidden;
  }
  #loadingModal .modal-body { padding: 36px 32px 30px; }
  .ie-modal-title    { font-size: 17px; font-weight: 700; color: #2d3748; margin-bottom: 2px; }
  .ie-modal-subtitle { font-size: 12px; color: #a0aec0; }

  /* ── KML section ── */
  #kml_section .card-header { background: linear-gradient(135deg, #0f9b58, #1a7a4a); }
  .format-toggle-btn {
    border-radius: 8px; font-size: 13px; font-weight: 600;
    padding: 8px 18px; transition: all 0.2s;
  }
  .format-toggle-btn.active { box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
</style>

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Export / Import Data</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </div>
                <div class="breadcrumb-item">Export / Import</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Manajemen Data</h2>
            <p class="section-lead">Export dan Import data untuk berbagai modul sistem.</p>

            {{-- ============================================================ --}}
            {{-- STEP 1: Pilih Menu --}}
            {{-- ============================================================ --}}
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-list"></i> Langkah 1 — Pilih Menu</h4>
                </div>
                <div class="card-body">
                    <form id="menuForm">
                        <div class="row">
                            @foreach($availableMenus as $key => $config)
                            <div class="col-md-4 mb-3">
                                <div class="custom-control custom-radio">
                                    <input type="radio"
                                           id="menu_{{ $key }}"
                                           name="menu_type"
                                           value="{{ $key }}"
                                           data-support-kml="{{ !empty($config['support_kml']) ? '1' : '0' }}"
                                           class="custom-control-input"
                                           required>
                                    <label class="custom-control-label" for="menu_{{ $key }}">
                                        {{ $config['label'] }}
                                        @if(!empty($config['support_kml']))
                                            <span class="badge badge-success ml-1" style="font-size:10px;">KML</span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- STEP 2a: Filter Kondisi Jalan --}}
            {{-- ============================================================ --}}
            <div class="card" id="filter_section" style="display: none;">
                <div class="card-header">
                    <h4><i class="fas fa-filter"></i> Langkah 2 — Filter Export (Kondisi Jalan)</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Penting:</strong> Sangat disarankan filter per tahun agar proses lebih cepat.
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label><strong>Filter Tahun:</strong> <span class="text-danger">*Disarankan</span></label>
                            <select name="year" id="export_year" class="form-control">
                                <option value="">⚠️ Semua Tahun (lambat)</option>
                                @php $currentYear = date('Y'); @endphp
                                @for($y = $currentYear; $y >= 2015; $y--)
                                    <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>
                                        Tahun {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label><strong>Filter Provinsi:</strong> <span class="text-muted">(Opsional)</span></label>
                            <select name="province_code" id="export_province" class="form-control">
                                <option value="">Semua Provinsi</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov->code }}">{{ $prov->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- STEP 2b: Pilihan Format KML (khusus Koordinat GPS) --}}
            {{-- ============================================================ --}}
            <div class="card" id="kml_section" style="display: none;">
                <div class="card-header text-white">
                    <h4 class="mb-0"><i class="fas fa-map-marked-alt"></i> Langkah 2 — Pilih Format & Filter (Koordinat GPS)</h4>
                </div>
                <div class="card-body">

                    {{-- Toggle Format --}}
                    <div class="mb-4">
                        <label class="d-block mb-2"><strong>Format File:</strong></label>
                        <div class="btn-group" role="group">
                            <button type="button" id="btn_format_xlsx"
                                    class="btn btn-outline-primary format-toggle-btn active"
                                    onclick="setFormat('xlsx')">
                                <i class="fas fa-file-excel"></i> Excel (.xlsx)
                            </button>
                            <button type="button" id="btn_format_kml"
                                    class="btn btn-outline-success format-toggle-btn"
                                    onclick="setFormat('kml')">
                                <i class="fas fa-map"></i> KML (.kml)
                                <span class="badge badge-light ml-1" style="font-size:10px;">Google Maps</span>
                            </button>
                        </div>
                        <small class="d-block text-muted mt-1" id="format_hint">
                            Excel: untuk edit data &amp; re-import. KML: untuk visualisasi di Google Maps / QGIS.
                        </small>
                    </div>

                    {{-- Filter KML (hanya muncul saat format KML dipilih) --}}
                    <div id="kml_filter_block" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Filter disarankan agar file KML tidak terlalu besar.
                            Kosongkan untuk export semua data alignment.
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><strong>Provinsi</strong> <span class="text-muted small">(opsional)</span></label>
                                    <select id="kml_province" class="form-control form-control-sm">
                                        <option value="">Semua Provinsi</option>
                                        @foreach($provinces as $prov)
                                            <option value="{{ $prov->code }}">{{ $prov->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><strong>Tahun</strong> <span class="text-muted small">(opsional)</span></label>
                                    <select id="kml_year" class="form-control form-control-sm">
                                        <option value="">Semua Tahun</option>
                                        @for($y = date('Y'); $y >= 2015; $y--)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><strong>Link No</strong> <span class="text-muted small">(opsional)</span></label>
                                    <input type="text" id="kml_link_no" class="form-control form-control-sm"
                                           placeholder="cth: 001">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><strong>Kabupaten</strong> <span class="text-muted small">(opsional)</span></label>
                                    <input type="text" id="kml_kabupaten" class="form-control form-control-sm"
                                           placeholder="cth: 3201">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info format --}}
                    <div id="xlsx_format_info">
                        <div class="alert alert-light border mb-0">
                            <i class="fas fa-file-excel text-success"></i>
                            <strong>Format Excel:</strong> Gunakan template yang sudah disediakan.
                            Cocok untuk menambah atau mengedit data alignment secara massal.
                        </div>
                    </div>
                    <div id="kml_format_info" style="display: none;">
                        <div class="alert alert-light border mb-0">
                            <i class="fas fa-map text-success"></i>
                            <strong>Format KML:</strong> File dapat langsung dibuka di
                            <strong>Google Earth</strong>, <strong>Google Maps</strong>, atau <strong>QGIS</strong>.
                            Setiap ruas jalan akan tampil sebagai garis merah di peta.
                        </div>
                    </div>

                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- STEP 3: Aksi --}}
            {{-- ============================================================ --}}
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-bolt"></i> Langkah 2 — Pilih Aksi</h4>
                </div>
                <div class="card-body">
                    <div class="row">

                        {{-- Kolom kiri: Template & Import --}}
                        <div class="col-md-6">
                            <div class="card border-primary h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-upload"></i> Import Data Baru</h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-3">
                                        Ikuti langkah ini untuk menambahkan data baru ke sistem:
                                    </p>

                                    <div class="d-flex align-items-start mb-3">
                                        <span class="badge badge-primary mr-2 mt-1" style="min-width:24px;">1</span>
                                        <div>
                                            <strong>Download Template</strong>
                                            <p class="text-muted small mb-2">
                                                Download file Excel kosong dengan format yang benar.
                                                Baris kuning adalah contoh, hapus sebelum diisi.
                                            </p>
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                    onclick="handleDownloadTemplate()">
                                                <i class="fas fa-file-download"></i> Download Template Excel
                                            </button>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="d-flex align-items-start mb-3">
                                        <span class="badge badge-secondary mr-2 mt-1" style="min-width:24px;">2</span>
                                        <div>
                                            <strong>Isi Template / Siapkan File</strong>
                                            <p class="text-muted small mb-0">
                                                Untuk Excel: isi data di bawah baris header. <strong>Jangan ubah nama kolom.</strong><br>
                                                Untuk KML: siapkan file .kml dari Google Earth atau hasil export sistem ini.
                                            </p>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="d-flex align-items-start">
                                        <span class="badge badge-success mr-2 mt-1" style="min-width:24px;">3</span>
                                        <div class="w-100">
                                            <strong>Upload & Import</strong>
                                            <p class="text-muted small mb-2">Upload file yang sudah disiapkan.</p>
                                            <button type="button" class="btn btn-primary btn-sm"
                                                    data-toggle="modal" data-target="#importModal">
                                                <i class="fas fa-upload"></i> Import Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kolom kanan: Export --}}
                        <div class="col-md-6">
                            <div class="card border-success h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-download"></i> Export Data Existing</h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-3">
                                        Download semua data yang sudah ada di database.
                                    </p>
                                    <button type="button" class="btn btn-success" onclick="handleExport()">
                                        <i class="fas fa-file-download"></i> Export Data
                                    </button>
                                    <div class="mt-3" id="export_time_estimate" style="display:none;">
                                        <small class="text-muted" id="export_estimate_text"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <strong><i class="fas fa-info-circle"></i> Catatan:</strong>
                        <ul class="mb-0 mt-2 small">
                            <li><strong>Ruas Jalan Kecamatan, Inventarisasi Jalan, Kondisi Jalan, Koordinat GPS</strong> — isi kolom <code>Link_No</code> bukan <code>Link_Id</code>. Sistem akan otomatis mencari ID-nya.</li>
                            <li>Pastikan <strong>Ruas Jalan sudah diimport terlebih dahulu</strong> sebelum mengimport data yang bergantung padanya.</li>
                            <li><strong>Import KML</strong> hanya tersedia untuk menu yang memiliki badge <span class="badge badge-success">KML</span>.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

{{-- ============================================================ --}}
{{-- Modal Import --}}
{{-- ============================================================ --}}
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-upload"></i> Import Data
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('import_export.import') }}" method="POST"
                  enctype="multipart/form-data" id="importForm">
                @csrf
                <input type="hidden" name="menu_type" id="import_menu_type">
                <input type="hidden" name="format"    id="import_format" value="xlsx">

                <div class="modal-body">
                    <div class="alert alert-primary">
                        <strong>Menu:</strong> <span id="selected_menu_label">-</span>
                        &nbsp;|&nbsp;
                        <strong>Format:</strong> <span id="selected_format_label">Excel</span>
                    </div>

                    {{-- Info khusus KML --}}
                    <div id="import_kml_info" class="alert alert-success small" style="display:none;">
                        <i class="fas fa-map"></i>
                        <strong>Import KML:</strong> Upload file <code>.kml</code> atau <code>.xml</code>.
                        Pastikan file berisi <code>&lt;Placemark&gt;</code> dengan <code>&lt;LineString&gt;</code>
                        dan ExtendedData yang valid (hasil export dari sistem ini atau Google Earth).
                    </div>

                    {{-- Info KML dari Google Earth --}}
                    <div id="import_kml_ge_info" class="alert alert-warning small" style="display:none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>KML dari Google Earth?</strong>
                        Pastikan nama Placemark mengikuti format: <code>Link [link_no] ([year])</code><br>
                        Contoh: <code>Link 001 (2024)</code>
                    </div>

                    <div class="alert alert-warning small" id="import_excel_warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Pastikan file menggunakan <strong>format template</strong> yang sudah didownload.
                        Jangan ubah nama kolom header.
                    </div>

                    <div class="form-group">
                        <label for="file"><strong>Pilih File</strong></label>
                        <input type="file" class="form-control-file" id="import_file" name="file"
                               accept=".xlsx,.xls" required>
                        <small class="form-text text-muted" id="import_file_hint">
                            Format: .xlsx / .xls — Maksimal 20MB
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnImportSubmit">
                        <i class="fas fa-upload"></i> Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- Modal Progress --}}
{{-- ============================================================ --}}
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog"
     data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 430px;" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="progress-modal-icon" id="pmIcon">
                    <i class="fas fa-cog fa-spin" id="pmIconInner"></i>
                </div>
                <div class="text-center mb-2">
                    <div class="ie-modal-title"    id="pmTitle">Sedang Memproses...</div>
                    <div class="ie-modal-subtitle" id="pmSub">Mohon tunggu, jangan tutup halaman ini.</div>
                </div>
                <div class="ie-progress-wrap">
                    <div class="ie-progress-fill" id="pmBar"></div>
                </div>
                <div class="ie-progress-meta">
                    <span class="step-label" id="pmStepLabel">Mempersiapkan...</span>
                    <span class="step-pct"   id="pmPct">0%</span>
                </div>
                <ul class="ie-steps" id="pmSteps"></ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── State format yang aktif ──────────────────────────────────────────
let activeFormat = 'xlsx'; // 'xlsx' | 'kml'

$(document).ready(function () {

    // ── Session alerts ────────────────────────────────────────────────
    @if(session('success'))
    Swal.fire({
        icon: 'success', title: 'Berhasil!',
        html: `{!! addslashes(session('success')) !!}`,
        confirmButtonColor: '#6777ef', confirmButtonText: 'OK',
        timer: 6000, timerProgressBar: true,
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error', title: 'Terjadi Kesalahan!',
        html: `{!! addslashes(session('error')) !!}`,
        confirmButtonColor: '#fc544b', confirmButtonText: 'Tutup',
    });
    @endif

    @if(session('import_warnings'))
    Swal.fire({
        icon: 'warning', title: 'Beberapa Baris Dilewati',
        html: `<div style="text-align:left;font-size:13px;max-height:250px;overflow-y:auto;padding:4px 2px;line-height:1.7;">
                {!! addslashes(session('import_warnings')) !!}
               </div>
               <p style="margin-top:12px;font-size:12px;color:#adb5bd;text-align:center;">
                <i class="fas fa-info-circle"></i> Cek log server untuk detail lengkap.
               </p>`,
        confirmButtonColor: '#ffa426', confirmButtonText: 'Mengerti', width: 560,
    });
    @endif

    // ── Perubahan menu → tampilkan section yang relevan ──────────────
    $('input[name="menu_type"]').on('change', function () {
        const isKondisiJalan = $(this).val() === 'kondisi_jalan';
        const isKmlSupported = $(this).data('support-kml') == '1';

        // Kondisi jalan → tampilkan filter tahun/provinsi
        if (isKondisiJalan) {
            $('#filter_section').slideDown();
        } else {
            $('#filter_section').slideUp();
        }

        // Menu dengan KML support → tampilkan section format
        if (isKmlSupported) {
            $('#kml_section').slideDown();
            // Reset ke xlsx saat ganti menu
            setFormat('xlsx');
        } else {
            $('#kml_section').slideUp();
            activeFormat = 'xlsx';
        }

        updateExportEstimate();
    });

    $('#export_year, #export_province').on('change', updateExportEstimate);

    // ── Import modal: set menu + format ──────────────────────────────
    $('#importModal').on('show.bs.modal', function (e) {
        const selected = document.querySelector('input[name="menu_type"]:checked');
        if (!selected) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning', title: 'Pilih Menu Dulu',
                text: 'Silakan pilih menu yang ingin diimport sebelum melanjutkan.',
                confirmButtonColor: '#6777ef', confirmButtonText: 'OK'
            });
            return;
        }

        const isKml = activeFormat === 'kml';

        document.getElementById('import_menu_type').value    = selected.value;
        document.getElementById('import_format').value       = activeFormat;
        document.getElementById('selected_menu_label').textContent =
            selected.nextElementSibling.textContent.trim();
        document.getElementById('selected_format_label').textContent =
            isKml ? '🗺️ KML' : '📊 Excel';

        // Tampilkan/sembunyikan info KML
        document.getElementById('import_kml_info').style.display    = isKml ? '' : 'none';
        document.getElementById('import_kml_ge_info').style.display = isKml ? '' : 'none';
        document.getElementById('import_excel_warning').style.display = isKml ? 'none' : '';

        // Set accept file input
        const fileInput = document.getElementById('import_file');
        const fileHint  = document.getElementById('import_file_hint');
        if (isKml) {
            fileInput.accept = '.kml,.xml';
            fileHint.textContent = 'Format: .kml / .xml — Maksimal 20MB';
        } else {
            fileInput.accept = '.xlsx,.xls';
            fileHint.textContent = 'Format: .xlsx / .xls — Maksimal 20MB';
        }
        fileInput.value = ''; // reset pilihan file
    });

    // ── Import form submit ────────────────────────────────────────────
    $('#importForm').on('submit', function () {
        const file    = document.getElementById('import_file');
        const isKml   = document.getElementById('import_format').value === 'kml';
        if (!file || !file.files.length) return;

        const fileSizeMB = (file.files[0].size / 1024 / 1024).toFixed(1);

        const stepsKml = [
            { label: 'Mengupload file KML ke server', pct: 20 },
            { label: 'Parsing XML / KML',             pct: 25 },
            { label: 'Lookup link master di database',pct: 25 },
            { label: 'Menyimpan titik alignment',     pct: 25 },
            { label: 'Menyelesaikan proses',          pct: 5  },
        ];
        const stepsExcel = [
            { label: 'Mengupload file ke server',  pct: 20 },
            { label: 'Membaca & parsing Excel',    pct: 25 },
            { label: 'Validasi data setiap baris', pct: 30 },
            { label: 'Menyimpan ke database',      pct: 20 },
            { label: 'Menyelesaikan proses',       pct: 5  },
        ];

        showProgressModal(
            'import',
            isKml ? stepsKml : stepsExcel,
            isKml ? 'Sedang Mengimport KML...' : 'Sedang Mengimport Data...',
            `${file.files[0].name} (${fileSizeMB} MB)`
        );

        $('#btnImportSubmit').prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin"></i> Mengimport...');
    });
});

// ── Toggle format xlsx/kml ────────────────────────────────────────────
function setFormat(fmt) {
    activeFormat = fmt;

    const btnXlsx = document.getElementById('btn_format_xlsx');
    const btnKml  = document.getElementById('btn_format_kml');
    const kmlFilter   = document.getElementById('kml_filter_block');
    const xlsxInfo    = document.getElementById('xlsx_format_info');
    const kmlInfo     = document.getElementById('kml_format_info');

    if (fmt === 'kml') {
        btnKml.classList.add('btn-success', 'active');
        btnKml.classList.remove('btn-outline-success');
        btnXlsx.classList.add('btn-outline-primary');
        btnXlsx.classList.remove('btn-primary', 'active');

        kmlFilter.style.display = '';
        xlsxInfo.style.display  = 'none';
        kmlInfo.style.display   = '';
    } else {
        btnXlsx.classList.add('btn-primary', 'active');
        btnXlsx.classList.remove('btn-outline-primary');
        btnKml.classList.add('btn-outline-success');
        btnKml.classList.remove('btn-success', 'active');

        kmlFilter.style.display = 'none';
        xlsxInfo.style.display  = '';
        kmlInfo.style.display   = 'none';
    }
}

// ── Download Template ─────────────────────────────────────────────────
function handleDownloadTemplate() {
    const selected = document.querySelector('input[name="menu_type"]:checked');
    if (!selected) {
        Swal.fire({
            icon: 'warning', title: 'Pilih Menu Dulu',
            text: 'Silakan pilih menu yang ingin didownload templatenya.',
            confirmButtonColor: '#6777ef', confirmButtonText: 'OK'
        });
        return;
    }

    showProgressModal('template', [
        { label: 'Menyiapkan struktur kolom', pct: 40 },
        { label: 'Membuat file Excel',        pct: 40 },
        { label: 'Mendownload file...',       pct: 20 },
    ], 'Menyiapkan Template...', 'File akan otomatis terdownload');

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("import_export.download_template") }}';
    form.innerHTML =
        `<input type="hidden" name="_token" value="{{ csrf_token() }}">
         <input type="hidden" name="menu_type" value="${selected.value}">`;
    document.body.appendChild(form);
    form.submit();

    setTimeout(() => $('#loadingModal').modal('hide'), 3500);
}

// ── Export Data ───────────────────────────────────────────────────────
function handleExport() {
    const selected = document.querySelector('input[name="menu_type"]:checked');
    if (!selected) {
        Swal.fire({
            icon: 'warning', title: 'Pilih Menu Dulu',
            text: 'Silakan pilih menu yang ingin diekspor.',
            confirmButtonColor: '#6777ef', confirmButtonText: 'OK'
        });
        return;
    }

    if (selected.value === 'kondisi_jalan' && !document.getElementById('export_year').value) {
        Swal.fire({
            icon: 'warning', title: 'Peringatan!',
            html: `<p>Export <strong>SEMUA</strong> kondisi jalan tanpa filter tahun bisa memakan waktu <strong>5–10 menit</strong>.</p>
                   <p class="text-muted small mb-0">Sangat disarankan pilih tahun terlebih dahulu.</p>`,
            showCancelButton: true,
            confirmButtonColor: '#28a745', cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-download"></i> Lanjutkan',
            cancelButtonText: 'Batal, Pilih Tahun', reverseButtons: true
        }).then(r => { if (r.isConfirmed) doExport(selected); });
        return;
    }

    doExport(selected);
}

function doExport(selected) {
    const isKml  = activeFormat === 'kml';
    const year   = isKml
        ? document.getElementById('kml_year')?.value
        : document.getElementById('export_year')?.value;
    const prov   = isKml
        ? document.getElementById('kml_province')?.value
        : document.getElementById('export_province')?.value;
    const linkNo = document.getElementById('kml_link_no')?.value;
    const kab    = document.getElementById('kml_kabupaten')?.value;

    let subtitle = isKml ? 'File KML akan otomatis terdownload' : 'File Excel akan otomatis terdownload';
    if (year)   subtitle += ` — Tahun ${year}`;
    if (linkNo) subtitle += ` — Link ${linkNo}`;

    const stepsKml = [
        { label: 'Query data alignment dari DB', pct: 30 },
        { label: 'Mengelompokkan per ruas jalan', pct: 30 },
        { label: 'Membuat file KML (XML)',        pct: 30 },
        { label: 'Mendownload file...',           pct: 10 },
    ];
    const stepsExcel = [
        { label: 'Membangun query database', pct: 20 },
        { label: 'Mengambil data dari DB',   pct: 35 },
        { label: 'Menulis ke file Excel',    pct: 30 },
        { label: 'Mendownload file...',      pct: 15 },
    ];

    showProgressModal(
        'export',
        isKml ? stepsKml : stepsExcel,
        isKml ? 'Sedang Membuat File KML...' : 'Sedang Mengexport Data...',
        subtitle
    );

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("import_export.export") }}';
    let inputs  = `<input type="hidden" name="_token" value="{{ csrf_token() }}">
                   <input type="hidden" name="menu_type" value="${selected.value}">
                   <input type="hidden" name="format"    value="${activeFormat}">`;

    if (selected.value === 'kondisi_jalan') {
        if (year) inputs += `<input type="hidden" name="year" value="${year}">`;
        if (prov) inputs += `<input type="hidden" name="province_code" value="${prov}">`;
    }

    if (isKml) {
        if (prov)   inputs += `<input type="hidden" name="province_code"  value="${prov}">`;
        if (year)   inputs += `<input type="hidden" name="year"           value="${year}">`;
        if (linkNo) inputs += `<input type="hidden" name="link_no"        value="${linkNo}">`;
        if (kab)    inputs += `<input type="hidden" name="kabupaten_code" value="${kab}">`;
    }

    form.innerHTML = inputs;
    document.body.appendChild(form);
    form.submit();
    setTimeout(() => $('#loadingModal').modal('hide'), 120000);
}

// ── Core progress modal ───────────────────────────────────────────────
function showProgressModal(type, steps, title, subtitle) {
    const cfg = {
        import:   { dotClass: '',             barClass: '',             pctClass: '',             iconClass: 'type-import',   fa: 'fa-database' },
        export:   { dotClass: 'export-dot',   barClass: 'export-bar',   pctClass: 'export-pct',   iconClass: 'type-export',   fa: 'fa-file-excel' },
        template: { dotClass: 'template-dot', barClass: 'template-bar', pctClass: 'template-pct', iconClass: 'type-template', fa: 'fa-table' },
    }[type];

    const pmIcon      = document.getElementById('pmIcon');
    const pmIconInner = document.getElementById('pmIconInner');
    const pmBar       = document.getElementById('pmBar');
    const pmPct       = document.getElementById('pmPct');
    const pmLabel     = document.getElementById('pmStepLabel');
    const pmTitle     = document.getElementById('pmTitle');
    const pmSub       = document.getElementById('pmSub');
    const pmSteps     = document.getElementById('pmSteps');

    pmIcon.className      = `progress-modal-icon ${cfg.iconClass}`;
    pmIconInner.className = `fas ${cfg.fa} fa-spin`;
    pmBar.className       = `ie-progress-fill ${cfg.barClass}`;
    pmPct.className       = `step-pct ${cfg.pctClass}`;
    pmTitle.textContent   = title;
    pmSub.textContent     = subtitle;
    pmBar.style.width     = '0%';
    pmPct.textContent     = '0%';
    pmLabel.textContent   = 'Mempersiapkan...';

    pmSteps.innerHTML = '';
    steps.forEach((s, i) => {
        pmSteps.innerHTML += `
            <li id="pms_${i}">
                <div class="sdot ${cfg.dotClass}" id="pmsdot_${i}">
                    <i class="fas fa-minus"></i>
                </div>
                <span>${s.label}</span>
            </li>`;
    });

    $('#loadingModal').modal('show');

    const totalDurMs = { import: 28000, export: 18000, template: 4000 }[type];
    let curPct = 0;

    function runStep(idx) {
        if (idx >= steps.length) {
            pmBar.style.width = '100%';
            pmPct.textContent = '100%';
            pmLabel.textContent = 'Selesai!';
            if (idx > 0) markDone(idx - 1);
            pmIconInner.classList.remove('fa-spin');
            return;
        }
        if (idx > 0) markDone(idx - 1);
        markActive(idx, cfg.dotClass);
        pmLabel.textContent = steps[idx].label;

        const targetPct = curPct + steps[idx].pct;
        const stepMs    = (steps[idx].pct / 100) * totalDurMs;
        const tickMs    = 40;
        const tickIncr  = (targetPct - curPct) / (stepMs / tickMs);

        const ticker = setInterval(() => {
            curPct += tickIncr;
            if (curPct >= targetPct) {
                curPct = targetPct;
                clearInterval(ticker);
                runStep(idx + 1);
            }
            pmBar.style.width = curPct + '%';
            pmPct.textContent = Math.round(curPct) + '%';
        }, tickMs);
    }

    function markActive(i, dotClass) {
        const li  = document.getElementById(`pms_${i}`);
        const dot = document.getElementById(`pmsdot_${i}`);
        if (!li) return;
        li.classList.add('active');
        dot.className = `sdot ${dotClass}`;
        dot.innerHTML = '<i class="fas fa-circle" style="font-size:8px;"></i>';
    }

    function markDone(i) {
        const li  = document.getElementById(`pms_${i}`);
        const dot = document.getElementById(`pmsdot_${i}`);
        if (!li) return;
        li.classList.remove('active');
        li.classList.add('done');
        dot.className = 'sdot';
        dot.innerHTML = '<i class="fas fa-check"></i>';
    }

    runStep(0);
}

// ── Estimasi export ───────────────────────────────────────────────────
function updateExportEstimate() {
    const selected = document.querySelector('input[name="menu_type"]:checked');
    if (!selected || selected.value !== 'kondisi_jalan') {
        $('#export_time_estimate').hide();
        return;
    }
    const year = document.getElementById('export_year')?.value;
    const prov = document.getElementById('export_province')?.value;

    let est = '';
    if (year && prov) est = '⚡ Estimasi: 5–15 detik';
    else if (year)    est = '⏱ Estimasi: 10–30 detik';
    else if (prov)    est = '⏳ Estimasi: 1–3 menit';
    else              est = '🐢 Estimasi: 5–10 menit (tidak disarankan)';

    $('#export_estimate_text').text(est);
    $('#export_time_estimate').show();
}
</script>
@endpush