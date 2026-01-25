@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Detail Kondisi Jalan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('kondisi-jalan.index') }}">Kondisi Jalan</a></div>
                <div class="breadcrumb-item active">Detail</div>
            </div>
        </div>

        <div class="section-body">
            {{-- Info Ruas --}}
            <div class="card card-primary">
                <div class="card-header">
                    <h4><i class="fas fa-road"></i> Informasi Ruas Jalan</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Kode Ruas</strong></td>
                                    <td>: {{ $ruas->link_code }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Ruas</strong></td>
                                    <td>: {{ $ruas->link_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>No. Ruas</strong></td>
                                    <td>: {{ $ruas->link_no }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Provinsi</strong></td>
                                    <td>: {{ $ruas->province->province_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kabupaten</strong></td>
                                    <td>: {{ $ruas->kabupaten->kabupaten_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>: 
                                        <span class="badge badge-info">
                                            {{ $ruas->statusRelation->code_description_ind ?? $ruas->status }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistik Keseluruhan --}}
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-road"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Segmen</h4>
                            </div>
                            <div class="card-body">
                                {{ $statistics['total_segments'] }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info">
                            <i class="fas fa-ruler-horizontal"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Panjang</h4>
                            </div>
                            <div class="card-body">
                                {{ number_format($statistics['total_length'], 2) }} km
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Rata-rata IRI</h4>
                            </div>
                            <div class="card-body">
                                {{ $statistics['avg_iri'] ? number_format($statistics['avg_iri'], 2) : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Rata-rata SDI</h4>
                            </div>
                            <div class="card-body">
                                {{ $statistics['avg_sdi'] ? number_format($statistics['avg_sdi'], 2) : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Distribusi Kondisi Jalan --}}
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-2">
                        <div class="card-stats">
                            <div class="card-stats-title">Kondisi Jalan
                            </div>
                            <div class="card-stats-items">
                                <div class="card-stats-item">
                                    <div class="card-stats-item-count">{{ $statistics['good_condition'] }}</div>
                                    <div class="card-stats-item-label">Baik</div>
                                </div>
                                <div class="card-stats-item">
                                    <div class="card-stats-item-count">{{ $statistics['fair_condition'] }}</div>
                                    <div class="card-stats-item-label">Sedang</div>
                                </div>
                                <div class="card-stats-item">
                                    <div class="card-stats-item-count">{{ $statistics['poor_condition'] }}</div>
                                    <div class="card-stats-item-label">Rusak Ringan</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-icon shadow-primary bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Segmen Baik</h4>
                            </div>
                            <div class="card-body">
                                {{ $statistics['good_condition'] }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-2">
                        <div class="card-chart">
                            <canvas id="balance-chart" height="80"></canvas>
                        </div>
                        <div class="card-icon shadow-warning" style="background-color: #FFD700; color: #fff;">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Segmen Sedang</h4>
                            </div>
                            <div class="card-body">
                                {{ $statistics['fair_condition'] }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-2">
                        <div class="card-chart">
                            <canvas id="sales-chart" height="80"></canvas>
                        </div>
                        <div class="card-icon shadow-danger" style="background-color: #FFA500; color: #fff;">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Rusak Ringan</h4>
                            </div>
                            <div class="card-body">
                                {{ $statistics['poor_condition'] }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-statistic-2">
                        <div class="card-chart">
                            <canvas id="sales-chart" height="80"></canvas>
                        </div>
                        <div class="card-icon shadow-dark bg-danger">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Rusak Berat</h4>
                            </div>
                            <div class="card-body">
                                {{ $statistics['very_poor_condition'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Analisis Kerusakan --}}
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-exclamation-triangle"></i> Analisis Kerusakan Jalan</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center mb-3">
                                        <h6>Total Luas Retak</h6>
                                        <h3 class="text-primary">{{ number_format($damage_analysis['total_crack_area'], 2) }} m²</h3>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center mb-3">
                                        <h6>Total Lubang</h6>
                                        <h3 class="text-danger">{{ $damage_analysis['total_potholes'] }}</h3>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center mb-3">
                                        <h6>Rata-rata Kedalaman Alur</h6>
                                        <h3 class="text-warning">{{ number_format($damage_analysis['avg_rutting_depth'], 2) }} cm</h3>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center mb-3">
                                        <h6>Segmen dengan Bleeding</h6>
                                        <h3 class="text-info">{{ $damage_analysis['segments_with_bleeding'] }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-chart-pie"></i> Distribusi SDI per Tahun</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tahun</th>
                                            <th>Rata-rata SDI</th>
                                            <th>Min SDI</th>
                                            <th>Max SDI</th>
                                            <th>Jumlah Segmen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sdi_by_year as $year => $data)
                                        <tr>
                                            <td><strong>{{ $year }}</strong></td>
                                            <td>{{ number_format($data['avg_sdi'], 2) }}</td>
                                            <td>{{ number_format($data['min_sdi'], 2) }}</td>
                                            <td>{{ number_format($data['max_sdi'], 2) }}</td>
                                            <td>{{ $data['count'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Detail Kondisi Jalan --}}
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-table"></i> Detail Kondisi Jalan per Segmen</h4>
                    <div class="card-header-action">
                        <button class="btn btn-primary" onclick="exportTableToExcel('conditionTable', 'Kondisi_Jalan_{{ $ruas->link_code }}')">
                            <i class="fas fa-download"></i> Export Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="conditionTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">Tahun</th>
                                    <th rowspan="2">Chainage</th>
                                    <th rowspan="2">Tipe Perkerasan</th>
                                    <th rowspan="2">Lebar (m)</th>
                                    <th colspan="4" class="text-center bg-info text-white">SDI</th>
                                    <th rowspan="2">Kategori</th>
                                    <th rowspan="2">Aksi</th>
                                </tr>
                                <tr>
                                    <th class="bg-light">SDI1</th>
                                    <th class="bg-light">SDI2</th>
                                    <th class="bg-light">SDI3</th>
                                    <th class="bg-light">SDI4</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($conditionsWithSDI as $index => $condition)
                                @php
                                    // ✅ DETEKSI TIPE PERKERASAN
                                    $pavementType = $condition->pavement ?? 'AS';
                                    $isNonAspal = in_array($pavementType, ['BT', 'BL', 'NA', 'TD']);
                                    
                                    $pavementLabels = [
                                        'AS' => 'Aspal',
                                        'BT' => 'Beton',
                                        'BL' => 'Blok',
                                        'NA' => 'Non Aspal',
                                        'TD' => 'Tak Dapat Dilalui'
                                    ];
                                    $pavementLabel = $pavementLabels[$pavementType] ?? 'Aspal';
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-center">{{ $condition->year }}</td>
                                    <td>{{ $condition->chainage_from }} - {{ $condition->chainage_to }}</td>
                                    <td class="text-center">
                                        {{-- ✅ BADGE TIPE PERKERASAN --}}
                                        @if($isNonAspal)
                                            <span class="badge badge-warning">
                                                <i class="fas fa-exclamation-triangle"></i> {{ $pavementLabel }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">{{ $pavementLabel }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ number_format($condition->inventory->pave_width ?? 0, 2) }}</td>
                                    
                                    {{-- ✅ SDI VALUES: Tampilkan N/A jika non-aspal --}}
                                    @if($isNonAspal)
                                        <td class="text-center text-muted">N/A</td>
                                        <td class="text-center text-muted">N/A</td>
                                        <td class="text-center text-muted">N/A</td>
                                        <td class="text-center text-muted">N/A</td>
                                    @else
                                        <td class="text-center">{{ number_format($condition->sdi_data['sdi1'], 2) }}</td>
                                        <td class="text-center">{{ number_format($condition->sdi_data['sdi2'], 2) }}</td>
                                        <td class="text-center">{{ number_format($condition->sdi_data['sdi3'], 2) }}</td>
                                        <td class="text-center font-weight-bold">{{ number_format($condition->sdi_data['sdi_final'], 2) }}</td>
                                    @endif
                                    
                                    <td class="text-center">
                                        @php
                                            $category = $condition->sdi_data['category'];
                                            $badgeClass = '';
                                            $badgeStyle = '';

                                            switch ($category) {
                                                case 'Baik':
                                                    $badgeClass = 'badge badge-success';
                                                    break;
                                                case 'Sedang':
                                                    $badgeClass = 'badge';
                                                    $badgeStyle = 'background-color: #FFD700; color: #fff;';
                                                    break;
                                                case 'Rusak Ringan':
                                                    $badgeClass = 'badge';
                                                    $badgeStyle = 'background-color: #FFA500; color: #fff;';
                                                    break;
                                                case 'Rusak Berat':
                                                    $badgeClass = 'badge badge-danger';
                                                    break;
                                                default:
                                                    $badgeClass = 'badge badge-secondary';
                                            }
                                        @endphp

                                        <span class="{{ $badgeClass }}" style="{{ $badgeStyle }}">
                                            {{ $category }}
                                        </span>
                                        
                                        {{-- ✅ BADGE TAMBAHAN UNTUK NON-ASPAL --}}
                                        @if($isNonAspal)
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> SDI tidak applicable
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{-- ✅ TOMBOL DETAIL: Disable untuk non-aspal --}}
                                        @if($isNonAspal)
                                            <button class="btn btn-sm btn-secondary" disabled title="Detail SDI hanya untuk perkerasan Aspal">
                                                <i class="fas fa-ban"></i> N/A
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-info" 
                                                    onclick='showDetailModal("{{ $condition->link_no }}", "{{ $condition->chainage_from }}", "{{ $condition->chainage_to }}", "{{ $condition->year }}")'>
                                                <i class="fas fa-calculator"></i> Detail SDI
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tombol Kembali --}}
            <div class="text-center mb-4">
                <a href="{{ route('kondisi-jalan.index') }}" class="btn btn-lg btn-primary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>

        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Inisialisasi DataTable
$(document).ready(function() {
    $('#conditionTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});

// Fungsi untuk menampilkan detail SDI LENGKAP
function showDetailModal(linkNo, chainageFrom, chainageTo, year) {
    console.log('showDetailModal called with:', {linkNo, chainageFrom, chainageTo, year});
    
    // Show loading
    Swal.fire({
        title: 'Memuat Detail...',
        html: '<div class="spinner-border text-primary" role="status"></div>',
        showConfirmButton: false,
        allowOutsideClick: false
    });

    // Ambil data detail dari server
    $.ajax({
        url: "{{ route('kondisi-jalan.getSegmentDetail') }}",
        type: "GET",
        data: {
            link_no: linkNo,
            chainage_from: chainageFrom,
            chainage_to: chainageTo,
            year: year
        },
        timeout: 10000,
        success: function(res) {
            if (res.success) {
                const data = res.data.sdi_detail;
                const condition = res.data.condition;
                
                // ✅ TAMBAHKAN VALIDASI NULL
                const linkCode = condition.link_no?.link_code || linkNo;
                const linkName = condition.link_no?.link_name || 'Ruas ' + linkNo;
                
                // ✅ CEK APAKAH NON-ASPAL
                if (data.final && data.final.note && data.final.note.includes('Non-Aspal')) {
                    Swal.fire({
                        title: `Detail Kondisi Jalan`,
                        html: `
                            <div class="text-center py-4">
                                <div class="alert alert-warning mb-3">
                                    <h5 class="text-center mb-2">
                                        <i class="fas fa-road"></i> 
                                        <strong>${linkCode} - ${linkName}</strong>
                                    </h5>
                                    <p class="mb-0 text-center">
                                        Chainage: <strong>${chainageFrom} - ${chainageTo}</strong> | 
                                        Tahun: <strong>${year}</strong>
                                    </p>
                                </div>
                                
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                    <h4>Perkerasan Non-Aspal</h4>
                                    <p class="mb-0">
                                        Metode SDI (Surface Distress Index) hanya berlaku untuk perkerasan <strong>Aspal</strong>.
                                        <br><br>
                                        Segmen ini menggunakan perkerasan <strong>${data.raw_data.pavement_type || 'Non-Aspal'}</strong>, 
                                        sehingga kategori kondisi otomatis ditetapkan sebagai:
                                    </p>
                                </div>
                                
                                <h2 class="text-danger mb-3">Rusak Berat</h2>
                                
                                <div class="alert alert-info text-left">
                                    <small>
                                        <strong><i class="fas fa-info-circle"></i> Catatan:</strong><br>
                                        Untuk perkerasan Beton, Blok, atau Non-Aspal lainnya, diperlukan metode evaluasi yang berbeda 
                                        seperti PCI (Pavement Condition Index) atau metode visual assessment.
                                    </small>
                                </div>
                            </div>
                        `,
                        width: 700,
                        confirmButtonText: '<i class="fas fa-times"></i> Tutup',
                        confirmButtonColor: '#6777ef',
                    });
                    return;
                }
                
                // ✅ JIKA ASPAL, TAMPILKAN DETAIL SDI LENGKAP
                const category = data.final.category;
                let badgeClass = 'badge-secondary';
                let customStyle = '';
                
                if (category === 'Baik') {
                    badgeClass = 'badge-success';
                } else if (category === 'Sedang') {
                    customStyle = 'background-color: #FFD700; color: #000;';
                } else if (category === 'Rusak Ringan') {
                    customStyle = 'background-color: #FFA500; color: #fff;';
                } else if (category === 'Rusak Berat') {
                    badgeClass = 'badge-dark';
                }

                Swal.fire({
                    title: `Detail Perhitungan SDI`,
                    html: `
                        <div class="text-left" style="max-height: 600px; overflow-y: auto;">
                            <div class="alert alert-info mb-3">
                                <h5 class="text-center mb-2">
                                    <i class="fas fa-road"></i> 
                                    <strong>${linkCode} - ${linkName}</strong>
                                </h5>
                                <p class="mb-0 text-center">
                                    Chainage: <strong>${chainageFrom} - ${chainageTo}</strong> | 
                                    Tahun: <strong>${year}</strong> |
                                    Perkerasan: <strong>Aspal</strong>
                                </p>
                            </div>
                            
                            <hr>
                            
                            <!-- DATA DASAR SEGMEN -->
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-info-circle"></i> Data Dasar Segmen
                            </h6>
                            <table class="table table-sm table-bordered mb-3">
                                <tr>
                                    <td width="50%"><strong>Lebar Jalan (Pave Width)</strong></td>
                                    <td><strong>${data.raw_data.pave_width.toFixed(2)} m</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Panjang Segmen</strong></td>
                                    <td>${data.raw_data.segment_length.toFixed(3)} m</td>
                                </tr>
                                <tr class="bg-light">
                                    <td><strong>Luas Total Segmen</strong></td>
                                    <td><strong>${data.raw_data.total_segment_area.toFixed(2)} m²</strong></td>
                                </tr>
                            </table>

                            <!-- TAHAP 1: LUAS RETAK -->
                            <div class="card mb-3 border-primary">
                                <div class="card-header bg-primary text-white py-2">
                                    <strong><i class="fas fa-layer-group"></i> TAHAP 1: Perhitungan Luas Retak (SDI1)</strong>
                                </div>
                                <div class="card-body p-2">
                                    <p class="mb-2"><small><em>${data.calculations.step1.formula}</em></small></p>
                                    
                                    <table class="table table-sm table-bordered mb-2">
                                        <tr>
                                            <td>Crack Depression Area</td>
                                            <td class="text-right">${data.raw_data.crack_dep_area.toFixed(2)} m²</td>
                                        </tr>
                                        <tr>
                                            <td>Other Crack Area</td>
                                            <td class="text-right">${data.raw_data.oth_crack_area.toFixed(2)} m²</td>
                                        </tr>
                                        <tr class="table-warning">
                                            <td><strong>Total Luas Retak (Aspal saja)</strong></td>
                                            <td class="text-right"><strong>${data.raw_data.total_crack_area.toFixed(2)} m²</strong></td>
                                        </tr>
                                        <tr class="table-info">
                                            <td><strong>% Luas Retak</strong></td>
                                            <td class="text-right"><strong>${data.raw_data.crack_percentage.toFixed(2)}%</strong></td>
                                        </tr>
                                    </table>
                                    
                                    <div class="alert alert-success mb-0 py-2">
                                        <strong>Hasil:</strong> ${data.calculations.step1.explanation}
                                        <div class="text-right mt-1"><strong>SDI1 = ${data.calculations.step1.value.toFixed(2)}</strong></div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAHAP 2: LEBAR RETAK -->
                            <div class="card mb-3 border-info">
                                <div class="card-header bg-info text-white py-2">
                                    <strong><i class="fas fa-arrows-alt-h"></i> TAHAP 2: Perhitungan Lebar Retak (SDI2)</strong>
                                </div>
                                <div class="card-body p-2">
                                    <p class="mb-2"><small><em>${data.calculations.step2.formula}</em></small></p>
                                    
                                    <table class="table table-sm table-bordered mb-2">
                                        <tr>
                                            <td><strong>Lebar Retak (Crack Width)</strong></td>
                                            <td class="text-right"><strong>${data.raw_data.crack_width.toFixed(2)} mm</strong></td>
                                        </tr>
                                    </table>
                                    
                                    <div class="alert alert-success mb-0 py-2">
                                        <strong>Hasil:</strong> ${data.calculations.step2.explanation}
                                        <div class="text-right mt-1"><strong>SDI2 = ${data.calculations.step2.value.toFixed(2)}</strong></div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAHAP 3: JUMLAH LUBANG -->
                            <div class="card mb-3 border-warning">
                                <div class="card-header bg-warning text-dark py-2">
                                    <strong><i class="fas fa-circle-notch"></i> TAHAP 3: Perhitungan Jumlah Lubang (SDI3)</strong>
                                </div>
                                <div class="card-body p-2">
                                    <p class="mb-2"><small><em>${data.calculations.step3.formula}</em></small></p>
                                    
                                    <table class="table table-sm table-bordered mb-2">
                                        <tr>
                                            <td><strong>Jumlah Lubang (Pothole)</strong></td>
                                            <td class="text-right"><strong>${data.raw_data.pothole_count}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Normalisasi per 100m</strong></td>
                                            <td class="text-right"><strong>${data.raw_data.normalized_potholes.toFixed(2)}</strong></td>
                                        </tr>
                                        <tr class="table-info">
                                            <td><strong>Penambahan Nilai</strong></td>
                                            <td class="text-right"><strong>+ ${data.calculations.step3.addition.toFixed(2)}</strong></td>
                                        </tr>
                                    </table>
                                    
                                    <div class="alert alert-success mb-0 py-2">
                                        <strong>Hasil:</strong> ${data.calculations.step3.explanation}
                                        <div class="text-right mt-1"><strong>SDI3 = ${data.calculations.step3.value.toFixed(2)}</strong></div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAHAP 4: KEDALAMAN ALUR -->
                            <div class="card mb-3 border-danger">
                                <div class="card-header bg-danger text-white py-2">
                                    <strong><i class="fas fa-water"></i> TAHAP 4: Perhitungan Kedalaman Alur Roda (SDI4 - Final)</strong>
                                </div>
                                <div class="card-body p-2">
                                    <p class="mb-2"><small><em>${data.calculations.step4.formula}</em></small></p>
                                    
                                    <table class="table table-sm table-bordered mb-2">
                                        <tr>
                                            <td><strong>Kedalaman Alur (Rutting Depth)</strong></td>
                                            <td class="text-right"><strong>${data.raw_data.rutting_depth.toFixed(2)} cm</strong></td>
                                        </tr>
                                        <tr class="table-info">
                                            <td><strong>Penambahan Nilai</strong></td>
                                            <td class="text-right"><strong>+ ${data.calculations.step4.addition.toFixed(2)}</strong></td>
                                        </tr>
                                    </table>
                                    
                                    <div class="alert alert-success mb-0 py-2">
                                        <strong>Hasil:</strong> ${data.calculations.step4.explanation}
                                        <div class="text-right mt-1"><strong>SDI4 = ${data.calculations.step4.value.toFixed(2)}</strong></div>
                                    </div>
                                </div>
                            </div>

                            <!-- HASIL AKHIR -->
                            <div class="card border-dark">
                                <div class="card-header bg-dark text-white text-center py-2">
                                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> HASIL AKHIR PERHITUNGAN SDI</h5>
                                </div>
                                <div class="card-body text-center p-3">
                                    <h2 class="text-primary mb-2">${data.final.sdi_final.toFixed(2)}</h2>
                                    <span class="badge ${badgeClass}" style="font-size: 18px; padding: 10px 20px; ${customStyle}">
                                        <i class="fas fa-flag"></i> ${data.final.category}
                                    </span>
                                    
                                    <div class="mt-3 text-left">
                                        <small class="text-muted">
                                            <strong>Referensi Kategori:</strong><br>
                                            • Baik: SDI < 50<br>
                                            • Sedang: 50 ≤ SDI < 100<br>
                                            • Rusak Ringan: 100 ≤ SDI < 150<br>
                                            • Rusak Berat: SDI ≥ 150
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- INFO TAMBAHAN -->
                            <div class="alert alert-light mt-3 mb-0">
                                <small>
                                    <i class="fas fa-info-circle"></i> 
                                    <strong>Catatan:</strong> Perhitungan SDI mengikuti Panduan Bina Marga untuk evaluasi kondisi permukaan jalan <strong>Aspal</strong>.
                                </small>
                            </div>
                        </div>
                    `,
                    width: 900,
                    confirmButtonText: '<i class="fas fa-times"></i> Tutup',
                    confirmButtonColor: '#6777ef',
                    customClass: {
                        container: 'sdi-detail-modal',
                        popup: 'sdi-detail-popup'
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'Gagal memuat data detail'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memuat data'
            });
        }
    });
}

// Fungsi export ke Excel
function exportTableToExcel(tableID, filename = '') {
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    
    filename = filename ? filename + '.xls' : 'excel_data.xls';
    downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    
    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob( blob, filename);
    }else{
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
        downloadLink.download = filename;
        downloadLink.click();
    }
}
</script>

<style>
.sdi-detail-modal .swal2-popup {
    font-size: 14px;
}
.sdi-detail-modal .table {
    font-size: 13px;
    margin-bottom: 0;
}
.sdi-detail-modal .table td {
    padding: 0.4rem;
}
.sdi-detail-modal .card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.sdi-detail-modal .card-header {
    font-size: 14px;
}
.badge-lg {
    padding: 0.5rem 1rem;
    font-size: 14px;
}
</style>
@endpush