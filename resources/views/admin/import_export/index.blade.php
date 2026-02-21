@extends('layouts.template')

@section('content')

<style>
  /* ── Progress Modal ── */
  .progress-modal-icon {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    font-size: 28px;
    color: #fff;
  }
  .progress-modal-icon.type-export   { background: linear-gradient(135deg, #28a745, #20c997); }
  .progress-modal-icon.type-import   { background: linear-gradient(135deg, #6777ef, #a855f7); }
  .progress-modal-icon.type-template { background: linear-gradient(135deg, #fd7e14, #ffc107); }

  .ie-progress-wrap {
    background: #edf0f7;
    border-radius: 50px;
    height: 10px;
    overflow: hidden;
    margin: 14px 0 6px;
  }

  .ie-progress-fill {
    height: 100%;
    border-radius: 50px;
    width: 0%;
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
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
  }
  .ie-progress-meta .step-label { font-size: 12px; color: #6c757d; }
  .ie-progress-meta .step-pct   { font-size: 13px; font-weight: 700; color: #6777ef; }
  .ie-progress-meta .step-pct.export-pct   { color: #28a745; }
  .ie-progress-meta .step-pct.template-pct { color: #fd7e14; }

  /* Steps list */
  .ie-steps { list-style: none; padding: 0; margin: 0; }
  .ie-steps li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 7px 0;
    font-size: 13px;
    color: #ced4da;
    border-bottom: 1px solid #f0f2f8;
    transition: color 0.3s;
  }
  .ie-steps li:last-child { border-bottom: none; }
  .ie-steps li.active { color: #343a40; font-weight: 600; }
  .ie-steps li.done   { color: #28a745; }

  .ie-steps .sdot {
    width: 24px; height: 24px;
    border-radius: 50%;
    border: 2px solid #dee2e6;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px;
    flex-shrink: 0;
    color: #ced4da;
    transition: all 0.3s;
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

  /* Modal card */
  #loadingModal .modal-content {
    border: none;
    border-radius: 18px;
    box-shadow: 0 24px 70px rgba(0,0,0,0.16);
    overflow: hidden;
  }
  #loadingModal .modal-body { padding: 36px 32px 30px; }

  .ie-modal-title    { font-size: 17px; font-weight: 700; color: #2d3748; margin-bottom: 2px; }
  .ie-modal-subtitle { font-size: 12px; color: #a0aec0; }
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
                                           class="custom-control-input"
                                           required>
                                    <label class="custom-control-label" for="menu_{{ $key }}">
                                        {{ $config['label'] }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- STEP 2: Filter (khusus kondisi jalan) --}}
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
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="handleDownloadTemplate()">
                                                <i class="fas fa-file-download"></i> Download Template
                                            </button>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="d-flex align-items-start mb-3">
                                        <span class="badge badge-secondary mr-2 mt-1" style="min-width:24px;">2</span>
                                        <div>
                                            <strong>Isi Template</strong>
                                            <p class="text-muted small mb-0">
                                                Buka file template, isi data baru di bawah baris header.
                                                <strong>Jangan ubah nama kolom.</strong>
                                                Kolom merah = wajib diisi.
                                            </p>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="d-flex align-items-start">
                                        <span class="badge badge-success mr-2 mt-1" style="min-width:24px;">3</span>
                                        <div class="w-100">
                                            <strong>Upload & Import</strong>
                                            <p class="text-muted small mb-2">Upload file template yang sudah diisi.</p>
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
                                        Download semua data yang sudah ada di database ke file Excel.
                                    </p>
                                    <button type="button" class="btn btn-success" onclick="handleExport()">
                                        <i class="fas fa-file-excel"></i> Export Data
                                    </button>
                                    <div class="mt-3" id="export_time_estimate" style="display:none;">
                                        <small class="text-muted" id="export_estimate_text"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <strong><i class="fas fa-info-circle"></i> Catatan untuk menu dengan Link_No:</strong>
                        <ul class="mb-0 mt-2 small">
                            <li><strong>Ruas Jalan Kecamatan, Inventarisasi Jalan, Kondisi Jalan, Koordinat GPS</strong> — isi kolom <code>Link_No</code> bukan <code>Link_Id</code>. Sistem akan otomatis mencari ID-nya.</li>
                            <li>Pastikan <strong>Ruas Jalan sudah diimport terlebih dahulu</strong> sebelum mengimport data yang bergantung padanya.</li>
                            <li>Kolom <strong>SDI_Value dan SDI_Category</strong> di template Kondisi Jalan boleh dikosongkan — dihitung otomatis oleh sistem.</li>
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
            <form action="{{ route('import_export.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="menu_type" id="import_menu_type">

                    <div class="alert alert-primary">
                        <strong>Menu:</strong> <span id="selected_menu_label">-</span>
                    </div>

                    <div class="alert alert-warning small">
                        <i class="fas fa-exclamation-triangle"></i>
                        Pastikan file yang diupload menggunakan <strong>format template</strong> yang sudah didownload.
                        Jangan ubah nama kolom header.
                    </div>

                    <div class="form-group">
                        <label for="file"><strong>Pilih File Excel (.xlsx / .xls)</strong></label>
                        <input type="file" class="form-control-file" id="file" name="file"
                               accept=".xlsx,.xls" required>
                        <small class="form-text text-muted">Maksimal 20MB</small>
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
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 430px;" role="document">
        <div class="modal-content">
            <div class="modal-body">

                {{-- Ikon --}}
                <div class="progress-modal-icon" id="pmIcon">
                    <i class="fas fa-cog fa-spin" id="pmIconInner"></i>
                </div>

                {{-- Judul & subtitle --}}
                <div class="text-center mb-2">
                    <div class="ie-modal-title"    id="pmTitle">Sedang Memproses...</div>
                    <div class="ie-modal-subtitle" id="pmSub">Mohon tunggu, jangan tutup halaman ini.</div>
                </div>

                {{-- Progress bar --}}
                <div class="ie-progress-wrap">
                    <div class="ie-progress-fill" id="pmBar"></div>
                </div>
                <div class="ie-progress-meta">
                    <span class="step-label" id="pmStepLabel">Mempersiapkan...</span>
                    <span class="step-pct"   id="pmPct">0%</span>
                </div>

                {{-- Steps --}}
                <ul class="ie-steps" id="pmSteps"></ul>

            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // ── Session alerts via SweetAlert2 ──────────────────────────────
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        html: `{!! addslashes(session('success')) !!}`,
        confirmButtonColor: '#6777ef',
        confirmButtonText: 'OK',
        timer: 6000,
        timerProgressBar: true,
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Terjadi Kesalahan!',
        html: `{!! addslashes(session('error')) !!}`,
        confirmButtonColor: '#fc544b',
        confirmButtonText: 'Tutup',
    });
    @endif

    @if(session('import_warnings'))
    Swal.fire({
        icon: 'warning',
        title: 'Beberapa Baris Dilewati',
        html: `
            <div style="text-align:left;font-size:13px;max-height:250px;overflow-y:auto;padding:4px 2px;line-height:1.7;">
                {!! addslashes(session('import_warnings')) !!}
            </div>
            <p style="margin-top:12px;font-size:12px;color:#adb5bd;text-align:center;">
                <i class="fas fa-info-circle"></i> Cek log server untuk detail lengkap.
            </p>
        `,
        confirmButtonColor: '#ffa426',
        confirmButtonText: 'Mengerti',
        width: 560,
    });
    @endif

    // ── Tampilkan/sembunyikan filter kondisi jalan ───────────────────
    $('input[name="menu_type"]').on('change', function () {
        if ($(this).val() === 'kondisi_jalan') {
            $('#filter_section').slideDown();
        } else {
            $('#filter_section').slideUp();
        }
        updateExportEstimate();
    });

    $('#export_year, #export_province').on('change', function () {
        updateExportEstimate();
    });

    // ── Import modal: validasi menu ──────────────────────────────────
    $('#importModal').on('show.bs.modal', function (e) {
        const selected = document.querySelector('input[name="menu_type"]:checked');
        if (!selected) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Menu Dulu',
                text: 'Silakan pilih menu yang ingin diimport sebelum melanjutkan.',
                confirmButtonColor: '#6777ef',
                confirmButtonText: 'OK'
            });
            return;
        }
        document.getElementById('import_menu_type').value = selected.value;
        document.getElementById('selected_menu_label').textContent =
            selected.nextElementSibling.textContent.trim();
    });

    // ── Import form submit: tampilkan progress ───────────────────────
    $('#importForm').on('submit', function () {
        const file = document.getElementById('file');
        if (!file || !file.files.length) return;

        const fileSizeMB = (file.files[0].size / 1024 / 1024).toFixed(1);

        showProgressModal('import', [
            { label: 'Mengupload file ke server',  pct: 20 },
            { label: 'Membaca & parsing Excel',    pct: 25 },
            { label: 'Validasi data setiap baris', pct: 30 },
            { label: 'Menyimpan ke database',      pct: 20 },
            { label: 'Menyelesaikan proses',       pct: 5  },
        ], 'Sedang Mengimport Data...', `${file.files[0].name} (${fileSizeMB} MB)`);

        $('#btnImportSubmit').prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin"></i> Mengimport...');
    });
});

// ── Download Template ────────────────────────────────────────────────
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

// ── Export Data ──────────────────────────────────────────────────────
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
            icon: 'warning',
            title: 'Peringatan!',
            html: `<p>Export <strong>SEMUA</strong> kondisi jalan tanpa filter tahun bisa memakan waktu <strong>5–10 menit</strong>.</p>
                   <p class="text-muted small mb-0">Sangat disarankan pilih tahun terlebih dahulu.</p>`,
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-download"></i> Lanjutkan',
            cancelButtonText: 'Batal, Pilih Tahun',
            reverseButtons: true
        }).then(r => { if (r.isConfirmed) doExport(selected); });
        return;
    }

    doExport(selected);
}

function doExport(selected) {
    const year  = document.getElementById('export_year')?.value;
    const prov  = document.getElementById('export_province')?.value;

    let subtitle = 'File akan otomatis terdownload';
    if (year && prov) subtitle = `Tahun ${year} — filter provinsi aktif`;
    else if (year)    subtitle = `Filter tahun: ${year}`;
    else if (prov)    subtitle = 'Filter provinsi aktif';
    else              subtitle = 'Semua data — proses mungkin lama';

    showProgressModal('export', [
        { label: 'Membangun query database', pct: 20 },
        { label: 'Mengambil data dari DB',   pct: 35 },
        { label: 'Menulis ke file Excel',    pct: 30 },
        { label: 'Mendownload file...',      pct: 15 },
    ], 'Sedang Mengexport Data...', subtitle);

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("import_export.export") }}';
    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <input type="hidden" name="menu_type" value="${selected.value}">`;

    if (selected.value === 'kondisi_jalan') {
        if (year) form.innerHTML += `<input type="hidden" name="year" value="${year}">`;
        if (prov) form.innerHTML += `<input type="hidden" name="province_code" value="${prov}">`;
    }

    document.body.appendChild(form);
    form.submit();
    setTimeout(() => $('#loadingModal').modal('hide'), 120000);
}

// ── Core progress modal ──────────────────────────────────────────────
// type: 'import' | 'export' | 'template'
// steps: [ { label, pct } ]   — pct = bobot bagian (jumlah = 100)
function showProgressModal(type, steps, title, subtitle) {
    const cfg = {
        import:   { dotClass: '',             barClass: '',             pctClass: '',             iconClass: 'type-import',   fa: 'fa-database' },
        export:   { dotClass: 'export-dot',   barClass: 'export-bar',   pctClass: 'export-pct',   iconClass: 'type-export',   fa: 'fa-file-excel' },
        template: { dotClass: 'template-dot', barClass: 'template-bar', pctClass: 'template-pct', iconClass: 'type-template', fa: 'fa-table' },
    }[type];

    // Reset UI
    const pmIcon    = document.getElementById('pmIcon');
    const pmIconInner = document.getElementById('pmIconInner');
    const pmBar     = document.getElementById('pmBar');
    const pmPct     = document.getElementById('pmPct');
    const pmLabel   = document.getElementById('pmStepLabel');
    const pmTitle   = document.getElementById('pmTitle');
    const pmSub     = document.getElementById('pmSub');
    const pmSteps   = document.getElementById('pmSteps');

    pmIcon.className    = `progress-modal-icon ${cfg.iconClass}`;
    pmIconInner.className = `fas ${cfg.fa} fa-spin`;
    pmBar.className     = `ie-progress-fill ${cfg.barClass}`;
    pmPct.className     = `step-pct ${cfg.pctClass}`;

    pmTitle.textContent = title;
    pmSub.textContent   = subtitle;
    pmBar.style.width   = '0%';
    pmPct.textContent   = '0%';
    pmLabel.textContent = 'Mempersiapkan...';

    // Render step items
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

    // Animasi bertahap
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
            pmBar.style.width   = curPct + '%';
            pmPct.textContent   = Math.round(curPct) + '%';
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

// ── Estimasi export ──────────────────────────────────────────────────
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