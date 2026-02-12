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
                                {{ number_format($statistics['total_length']) }} km
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
                                        <h3 class="text-primary">{{ number_format($damage_analysis['total_crack_area']) }} m²</h3>
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

            {{-- ✅ TABEL BARU: RAW DATA (Luas Retak, Lebar Retak, Jumlah Lubang, Alur) --}}
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
                                    <th rowspan="2">Chainage</th>
                                    <th rowspan="2">Tipe Perkerasan</th>
                                    <th rowspan="2">Lebar (m)</th>
                                    <th colspan="4" class="text-center bg-warning text-dark">Data Survei Lapangan</th>
                                    <th rowspan="2">Kategori</th>
                                    <th rowspan="2">Aksi</th>
                                </tr>
                                <tr>
                                    <th class="bg-light">Luas Retak (m²)</th>
                                    <th class="bg-light">Lebar Retak</th>
                                    <th class="bg-light">Jumlah Lubang</th>
                                    <th class="bg-light">Alur Roda</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($conditionsWithSDI as $index => $condition)
                                @php
                                    // Deteksi tipe perkerasan
                                    $pavementType = $condition->pavement ?? 'Asphalt';
                                    $isNonAspal = in_array($pavementType, ['Block', 'Concrete', 'Unpaved', 'Impassable']);
                                    
                                    // Mapping label
                                    $pavementLabels = [
                                        'Asphalt' => 'Aspal',
                                        'Concrete' => 'Beton',
                                        'Block' => 'Blok',
                                        'Unpaved' => 'Non Aspal',
                                        'Impassable' => 'Tak Dapat Dilalui'
                                    ];
                                    $pavementLabel = $pavementLabels[$pavementType] ?? 'Aspal';
                                    
                                    $pavementBadgeClass = [
                                        'Asphalt' => 'badge-secondary',
                                        'Block' => 'badge-info',
                                        'Concrete' => 'badge-primary',
                                        'Unpaved' => 'badge-warning',
                                        'Impassable' => 'badge-danger',
                                    ][$pavementType] ?? 'badge-secondary';

                                    // ✅ AMBIL DATA RAW DARI DATABASE
                                    $totalCrackArea = floatval($condition->crack_dep_area ?? 0) + floatval($condition->oth_crack_area ?? 0);
                                    $crackWidthBobot = intval($condition->crack_width ?? 1);
                                    $potholeCount = intval($condition->pothole_count ?? 0);
                                    $ruttingDepthBobot = intval($condition->rutting_depth ?? 1);

                                    // Mapping bobot ke label
                                    $crackWidthLabels = [
                                        1 => 'Tidak ada',
                                        2 => '< 1mm',
                                        3 => '1-3mm',
                                        4 => '> 3mm'
                                    ];
                                    
                                    $ruttingDepthLabels = [
                                        1 => 'Tidak ada',
                                        2 => '< 1cm',
                                        3 => '1-3cm',
                                        4 => '> 3cm'
                                    ];

                                    $crackWidthLabel = $crackWidthLabels[$crackWidthBobot] ?? 'N/A';
                                    $ruttingDepthLabel = $ruttingDepthLabels[$ruttingDepthBobot] ?? 'N/A';
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $condition->chainage_from }} - {{ $condition->chainage_to }}</td>
                                    
                                    {{-- Tipe Perkerasan --}}
                                    <td class="text-center">
                                        @if($isNonAspal)
                                            <span class="badge {{ $pavementBadgeClass }}">
                                                <i class="fas fa-exclamation-triangle"></i> {{ $pavementLabel }}
                                            </span>
                                        @else
                                            <span class="badge {{ $pavementBadgeClass }}">
                                                {{ $pavementLabel }}
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <td class="text-center">{{ number_format($condition->inventory->pave_width ?? 0, 2) }}</td>
                                    
                                    {{-- ✅ DATA RAW: Luas Retak --}}
                                    @if($isNonAspal)
                                        <td class="text-center text-muted">N/A</td>
                                    @else
                                        <td class="text-center">
                                            <strong>{{ number_format($totalCrackArea, 2) }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                ({{ number_format($condition->crack_dep_area ?? 0, 2) }} + {{ number_format($condition->oth_crack_area ?? 0, 2) }})
                                            </small>
                                        </td>
                                    @endif
                                    
                                    {{-- ✅ DATA RAW: Lebar Retak --}}
                                    @if($isNonAspal)
                                        <td class="text-center text-muted">N/A</td>
                                    @else
                                        <td class="text-center">
                                            <span class="badge badge-{{ $crackWidthBobot >= 4 ? 'danger' : ($crackWidthBobot >= 3 ? 'warning' : 'success') }}">
                                                {{ $crackWidthLabel }}
                                            </span>
                                            <br>
                                            <small class="text-muted">(Bobot {{ $crackWidthBobot }})</small>
                                        </td>
                                    @endif
                                    
                                    {{-- ✅ DATA RAW: Jumlah Lubang --}}
                                    @if($isNonAspal)
                                        <td class="text-center text-muted">N/A</td>
                                    @else
                                        <td class="text-center">
                                            <strong class="text-{{ $potholeCount > 50 ? 'danger' : ($potholeCount > 10 ? 'warning' : 'success') }}">
                                                {{ $potholeCount }} buah
                                            </strong>
                                        </td>
                                    @endif
                                    
                                    {{-- ✅ DATA RAW: Alur Roda --}}
                                    @if($isNonAspal)
                                        <td class="text-center text-muted">N/A</td>
                                    @else
                                        <td class="text-center">
                                            <span class="badge badge-{{ $ruttingDepthBobot >= 4 ? 'danger' : ($ruttingDepthBobot >= 3 ? 'warning' : 'success') }}">
                                                {{ $ruttingDepthLabel }}
                                            </span>
                                            <br>
                                            <small class="text-muted">(Bobot {{ $ruttingDepthBobot }})</small>
                                        </td>
                                    @endif
                                    
                                    {{-- Kategori SDI --}}
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
                                        
                                        @if($isNonAspal)
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> SDI tidak applicable
                                            </small>
                                        @endif
                                    </td>
                                    
                                    {{-- Aksi --}}
                                    <td class="text-center">
                                        @if($isNonAspal)
                                            <button class="btn btn-sm btn-secondary" disabled 
                                                    title="Detail SDI hanya untuk perkerasan Aspal">
                                                <i class="fas fa-ban"></i> N/A
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-info" 
                                                    onclick='showDetailModal("{{ $condition->link_no }}", "{{ $condition->chainage_from }}", "{{ $condition->chainage_to }}", "{{ $condition->year }}")'>
                                                <i class="fas fa-calculator"></i> Detail SDI
                                            </button>
                                        @endif
                                        
                                        <a href="{{ route('kondisi-jalan.edit', [$condition->link_no, $condition->chainage_from, $condition->chainage_to, $condition->year]) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
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
// ========================================
// ✅ SMART NUMBER FORMATTER
// ========================================
function smartFormat(value, type = 'auto') {
    const num = parseFloat(value);
    if (isNaN(num)) return '0';
    
    switch(type) {
        case 'integer':
            return Math.round(num).toString();
        
        case 'decimal1':
            return num % 1 === 0 ? num.toString() : num.toFixed(1);
        
        case 'decimal2':
            return num.toFixed(2);
        
        case 'auto':
        default:
            if (Number.isInteger(num)) {
                return num.toString();
            }
            const decimalPart = num.toString().split('.')[1];
            if (decimalPart && decimalPart.length === 1) {
                return num.toFixed(1);
            }
            return num.toFixed(2);
    }
}

// ========================================
// INISIALISASI DATATABLE
// ========================================
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

// ========================================
// ✅ FUNGSI UTAMA: SHOW DETAIL MODAL
// ========================================
function showDetailModal(linkNo, chainageFrom, chainageTo, year) {
    console.log('🔍 showDetailModal called with:', {linkNo, chainageFrom, chainageTo, year});
    
    Swal.fire({
        title: 'Memuat Detail...',
        html: '<div class="spinner-border text-primary" role="status"></div>',
        showConfirmButton: false,
        allowOutsideClick: false
    });

    $.ajax({
        url: "{{ route('kondisi-jalan.getSegmentDetail') }}",
        type: "GET",
        data: {
            link_no: linkNo,
            chainage_from: chainageFrom,
            chainage_to: chainageTo,
            year: year
        },
        timeout: 15000,
        success: function(res) {
            console.log('✅ Full response:', res);
            
            if (res.success) {
                const sdiDetail = res.data?.sdi_detail || {};
                const condition = res.data?.condition || {};
                
                const rawData = sdiDetail.raw_data || {};
                const calculations = sdiDetail.explanations || {};
                const finalData = sdiDetail.final || {};
                
                const linkInfo = condition.link_no || {};
                const linkCode = linkInfo.link_code || linkNo;
                const linkName = linkInfo.link_name || 'Ruas ' + linkNo;
                
                if (!rawData.pave_width || rawData.pave_width === undefined) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Tidak Lengkap',
                        text: 'Data inventaris tidak tersedia'
                    });
                    return;
                }
                
                const pavementType = rawData.pavement_type || 'Asphalt';
                const isNonAspal = ['Concrete', 'Block', 'Unpaved', 'Impassable'].includes(pavementType);
                
                if (isNonAspal || finalData.note?.includes('Non-Aspal')) {
                    showNonAspalModal(linkCode, linkName, chainageFrom, chainageTo, year, pavementType);
                    return;
                }
                
                showAspalDetailModal(linkCode, linkName, chainageFrom, chainageTo, year, rawData, calculations, finalData);
                
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'Gagal memuat data detail'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ AJAX Error:', {status, error, response: xhr.responseText});
            
            let errorMessage = 'Terjadi kesalahan saat memuat data';
            if (status === 'timeout') errorMessage = 'Request timeout - Server membutuhkan waktu terlalu lama';
            else if (xhr.status === 404) errorMessage = 'Route tidak ditemukan (404)';
            else if (xhr.status === 500) errorMessage = 'Server error (500) - Periksa log backend';
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: `<p>${errorMessage}</p><small>Status: ${xhr.status}</small>`,
            });
        }
    });
}

// ========================================
// MODAL UNTUK PERKERASAN NON-ASPAL
// ========================================
function showNonAspalModal(linkCode, linkName, chainageFrom, chainageTo, year, pavementType) {
    const pavementNames = {
        'Concrete': 'Beton',
        'Block': 'Blok',
        'Unpaved': 'Non Aspal',
        'Impassable': 'Tak Dapat Dilalui'
    };
    
    const pavementLabel = pavementNames[pavementType] || 'Non-Aspal';
    
    const pavementIcons = {
        'Concrete': 'fa-th-large',
        'Block': 'fa-th',
        'Unpaved': 'fa-mountain',
        'Impassable': 'fa-ban'
    };
    
    const icon = pavementIcons[pavementType] || 'fa-exclamation-triangle';
    
    Swal.fire({
        title: 'Detail Kondisi Jalan',
        html: `
            <div class="text-center py-4">
                <div class="alert alert-info mb-3">
                    <h5 class="mb-2">
                        <i class="fas fa-road"></i> 
                        <strong>${linkCode} - ${linkName}</strong>
                    </h5>
                    <p class="mb-0">
                        Chainage: <strong>${chainageFrom} - ${chainageTo}</strong> | 
                        Tahun: <strong>${year}</strong>
                    </p>
                </div>
                
                <div class="alert alert-danger">
                    <i class="fas ${icon} fa-3x mb-3"></i>
                    <h4>Perkerasan Non-Aspal</h4>
                    <p class="mb-0">
                        Metode SDI hanya berlaku untuk perkerasan <strong>Aspal</strong>.<br><br>
                        Segmen ini menggunakan perkerasan: <strong>${pavementLabel}</strong><br>
                        <small class="text-muted">(Database Code: ${pavementType})</small>
                    </p>
                </div>
                
                <h2 class="text-danger mb-3">Rusak Berat</h2>
                
                <div class="alert alert-light">
                    <p class="mb-0">
                        <i class="fas fa-info-circle"></i> 
                        Untuk perkerasan non-aspal, kondisi jalan secara default 
                        dikategorikan sebagai <strong>"Rusak Berat"</strong> 
                        karena tidak dapat dinilai menggunakan metode SDI.
                    </p>
                </div>
            </div>
        `,
        width: 700,
        confirmButtonText: '<i class="fas fa-times"></i> Tutup',
        confirmButtonColor: '#6777ef',
    });
}

// ========================================
// MODAL DETAIL UNTUK PERKERASAN ASPAL
// ========================================
function showAspalDetailModal(linkCode, linkName, chainageFrom, chainageTo, year, rawData, calculations, finalData) {
    console.log('🎯 Building Aspal Detail Modal with:', {
        linkCode, linkName, chainageFrom, chainageTo, year,
        hasRawData: !!rawData,
        hasCalculations: !!calculations,
        hasFinalData: !!finalData,
        calculationsKeys: Object.keys(calculations)
    });

    const category = finalData.category || 'Data Tidak Lengkap';
    const sdiFinal = parseFloat(finalData.sdi_final) || 0;
    
    let badgeClass = 'badge-secondary';
    let customStyle = '';
    let badgeIcon = 'fa-question-circle';
    
    if (category === 'Baik') {
        badgeClass = 'badge-success';
        badgeIcon = 'fa-check-circle';
    } else if (category === 'Sedang') {
        customStyle = 'background-color: #FFD700; color: #000;';
        badgeIcon = 'fa-exclamation-circle';
    } else if (category === 'Rusak Ringan') {
        customStyle = 'background-color: #FFA500; color: #fff;';
        badgeIcon = 'fa-times-circle';
    } else if (category === 'Rusak Berat') {
        badgeClass = 'badge-danger';
        badgeIcon = 'fa-ban';
    }

    const html = `
        <div class="text-left" style="max-height: 650px; overflow-y: auto; padding: 15px;">
            <div class="alert alert-info mb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white;">
                <h5 class="text-center mb-2" style="color: white;">
                    <i class="fas fa-road"></i> 
                    <strong>${linkCode} - ${linkName}</strong>
                </h5>
                <p class="mb-0 text-center" style="color: white;">
                    Chainage: <strong>${chainageFrom} - ${chainageTo}</strong> | 
                    Tahun: <strong>${year}</strong> | 
                    Perkerasan: <strong>Aspal</strong>
                </p>
            </div>
            
            <hr style="border-top: 2px solid #ddd;">
            
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <h6 class="mb-3" style="color: #667eea; font-weight: bold;">
                    <i class="fas fa-info-circle"></i> DATA DASAR SEGMEN
                </h6>
                <table class="table table-sm table-bordered mb-0" style="background: white;">
                    <tr>
                        <td width="50%"><strong>Lebar Jalan</strong></td>
                        <td><strong style="color: #667eea;">${smartFormat(rawData.pave_width, 'decimal1')} m</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Panjang Segmen</strong></td>
                        <td>${smartFormat(rawData.segment_length_meter, 'decimal1')} m</td>
                    </tr>
                    <tr style="background: #e3f2fd;">
                        <td><strong>Luas Total Segmen</strong></td>
                        <td><strong style="color: #1976d2;">${smartFormat(rawData.total_segment_area, 'decimal2')} m²</strong></td>
                    </tr>
                </table>
            </div>

            ${buildStep1HTML(rawData, calculations.step1)}
            ${buildStep2HTML(rawData, calculations.step2)}
            ${buildStep3HTML(rawData, calculations.step3)}
            ${buildStep4HTML(rawData, calculations.step4)}
            ${buildFinalResultHTML(sdiFinal, category, badgeClass, customStyle, badgeIcon)}
        </div>
    `;

    Swal.fire({
        title: '<i class="fas fa-calculator"></i> Detail Perhitungan SDI',
        html: html,
        width: 950,
        confirmButtonText: '<i class="fas fa-times"></i> Tutup',
        confirmButtonColor: '#6777ef',
    });
}

// ========================================
// HELPER: BUILD STEP 1 HTML (LUAS RETAK)
// ========================================
function buildStep1HTML(rawData, step1) {
    if (!step1) {
        return '<div class="alert alert-warning">Data Step 1 tidak tersedia</div>';
    }

    return `
        <div class="card mb-3" style="border-left: 5px solid #4CAF50;">
            <div class="card-header" style="background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 12px 15px;">
                <strong><i class="fas fa-layer-group"></i> TAHAP 1: LUAS RETAK (SDI1)</strong>
            </div>
            <div class="card-body p-3">
                <div class="alert alert-light mb-3" style="background: #f5f5f5; border-left: 3px solid #4CAF50;">
                    <strong><i class="fas fa-calculator"></i> Formula:</strong><br>
                    <code>${step1.formula || '% Retak = (Total Luas Retak / Luas Segmen) × 100'}</code>
                </div>

                <h6 class="mb-2" style="color: #4CAF50;"><i class="fas fa-database"></i> Data:</h6>
                <table class="table table-sm table-bordered mb-3">
                    <tr>
                        <td>Crack Depression Area</td>
                        <td class="text-right"><strong>${smartFormat(rawData.crack_dep_area, 'decimal2')} m²</strong></td>
                    </tr>
                    <tr>
                        <td>Other Crack Area</td>
                        <td class="text-right"><strong>${smartFormat(rawData.oth_crack_area, 'decimal2')} m²</strong></td>
                    </tr>
                    <tr style="background: #fff3cd;">
                        <td><strong>Total Luas Retak</strong></td>
                        <td class="text-right"><strong>${smartFormat(rawData.total_crack_area, 'decimal2')} m²</strong></td>
                    </tr>
                </table>

                <h6 class="mb-2" style="color: #4CAF50;"><i class="fas fa-cogs"></i> Perhitungan:</h6>
                <div class="alert alert-light mb-3" style="background: #e8f5e9;">
                    <code>
                        % Retak = (${smartFormat(rawData.total_crack_area, 'decimal2')} / ${smartFormat(rawData.total_segment_area, 'decimal2')}) × 100<br>
                        % Retak = <strong>${smartFormat(rawData.crack_percentage, 'decimal2')}%</strong>
                    </code>
                </div>

                <div class="alert alert-success mb-3">
                    <i class="fas fa-check-circle"></i> ${step1.explanation || '-'}
                </div>

                <div class="text-center p-3" style="background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); border-radius: 8px;">
                    <h4 class="mb-0" style="color: white;">
                        <i class="fas fa-flag-checkered"></i> HASIL SDI1 = <strong>${smartFormat(step1.value, 'decimal2')}</strong>
                    </h4>
                </div>
            </div>
        </div>
    `;
}

// ========================================
// HELPER: BUILD STEP 2 HTML (LEBAR RETAK)
// ========================================
function buildStep2HTML(rawData, step2) {
    if (!step2) {
        return '<div class="alert alert-warning">Data Step 2 tidak tersedia</div>';
    }

    const crackWidthBobot = parseInt(rawData.crack_width_bobot) || 0;
    const crackWidthLabels = {
        0: 'Tidak ada data', 
        1: 'Tidak ada retak',
        2: 'Halus < 1mm', 
        3: 'Sedang 1-3mm', 
        4: 'Lebar > 3mm'
    };

    return `
        <div class="card mb-3" style="border-left: 5px solid #2196F3;">
            <div class="card-header" style="background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%); color: white; padding: 12px 15px;">
                <strong><i class="fas fa-arrows-alt-h"></i> TAHAP 2: LEBAR RETAK (SDI2)</strong>
            </div>
            <div class="card-body p-3">
                <div class="alert alert-light mb-3" style="background: #f5f5f5; border-left: 3px solid #2196F3;">
                    <strong><i class="fas fa-calculator"></i> Formula:</strong><br>
                    <code>${step2.formula || '• Lebar ≤ 3mm → SDI2 = SDI1<br>• Lebar > 3mm → SDI2 = SDI1 × 2'}</code>
                </div>

                <table class="table table-sm table-bordered mb-3">
                    <tr>
                        <td><strong>Lebar Retak</strong></td>
                        <td class="text-right">
                            <span class="badge badge-info">${crackWidthLabels[crackWidthBobot]}</span>
                            <small class="text-muted ml-2">(bobot ${crackWidthBobot})</small>
                        </td>
                    </tr>
                </table>

                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle"></i> ${step2.explanation || '-'}
                </div>

                <div class="text-center p-3" style="background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%); border-radius: 8px;">
                    <h4 class="mb-0" style="color: white;">
                        <i class="fas fa-flag-checkered"></i> HASIL SDI2 = <strong>${smartFormat(step2.value, 'decimal2')}</strong>
                    </h4>
                </div>
            </div>
        </div>
    `;
}

// ========================================
// HELPER: BUILD STEP 3 HTML (JUMLAH LUBANG)
// ========================================
function buildStep3HTML(rawData, step3) {
    if (!step3) {
        return '<div class="alert alert-warning">Data Step 3 tidak tersedia</div>';
    }

    return `
        <div class="card mb-3" style="border-left: 5px solid #FF9800;">
            <div class="card-header" style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%); color: white; padding: 12px 15px;">
                <strong><i class="fas fa-circle-notch"></i> TAHAP 3: JUMLAH LUBANG (SDI3)</strong>
            </div>
            <div class="card-body p-3">
                <div class="alert alert-light mb-3" style="background: #f5f5f5; border-left: 3px solid #FF9800;">
                    <strong><i class="fas fa-calculator"></i> Formula:</strong><br>
                    <code>${step3.formula || 'Normalisasi = (Jumlah Lubang / Panjang Segmen) × 100'}</code>
                </div>

                <table class="table table-sm table-bordered mb-3">
                    <tr>
                        <td>Jumlah Lubang</td>
                        <td class="text-right"><strong>${smartFormat(rawData.pothole_count, 'integer')} buah</strong></td>
                    </tr>
                    <tr>
                        <td>Panjang Segmen</td>
                        <td class="text-right">${smartFormat(rawData.segment_length_meter, 'decimal1')} m</td>
                    </tr>
                </table>

                <div class="alert alert-light mb-3" style="background: #fff3e0;">
                    <code>
                        Normalized = (${smartFormat(rawData.pothole_count, 'integer')} / ${smartFormat(rawData.segment_length_meter, 'decimal1')}) × 100<br>
                        Normalized = <strong>${smartFormat(rawData.pothole_per_100m, 'decimal1')} per 100m</strong>
                    </code>
                </div>

                <div class="alert alert-warning mb-3">
                    <i class="fas fa-exclamation-triangle"></i> ${step3.explanation || '-'}
                    ${step3.addition ? `<br><strong>Penambahan: +${smartFormat(step3.addition, 'decimal2')}</strong>` : ''}
                </div>

                <div class="text-center p-3" style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%); border-radius: 8px;">
                    <h4 class="mb-0" style="color: white;">
                        <i class="fas fa-flag-checkered"></i> HASIL SDI3 = <strong>${smartFormat(step3.value, 'decimal2')}</strong>
                    </h4>
                </div>
            </div>
        </div>
    `;
}

// ========================================
// HELPER: BUILD STEP 4 HTML (KEDALAMAN ALUR)
// ========================================
function buildStep4HTML(rawData, step4) {
    if (!step4) {
        return '<div class="alert alert-warning">Data Step 4 tidak tersedia</div>';
    }

    const ruttingDepthBobot = parseInt(rawData.rutting_depth_bobot) || 0;
    const ruttingLabels = {
        0: 'Tidak ada data', 
        1: 'Tidak ada alur',
        2: 'Kedalaman < 1cm', 
        3: 'Kedalaman 1-3cm', 
        4: 'Kedalaman > 3cm'
    };

    return `
        <div class="card mb-3" style="border-left: 5px solid #f44336;">
            <div class="card-header" style="background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); color: white; padding: 12px 15px;">
                <strong><i class="fas fa-water"></i> TAHAP 4: KEDALAMAN ALUR (SDI4)</strong>
            </div>
            <div class="card-body p-3">
                <div class="alert alert-light mb-3" style="background: #f5f5f5; border-left: 3px solid #f44336;">
                    <strong><i class="fas fa-calculator"></i> Formula:</strong><br>
                    <code>${step4.formula || '• < 1cm → Tambah 2.5<br>• 1-3cm → Tambah 10<br>• > 3cm → Tambah 20'}</code>
                </div>

                <table class="table table-sm table-bordered mb-3">
                    <tr>
                        <td><strong>Kedalaman Alur</strong></td>
                        <td class="text-right">
                            <span class="badge badge-danger">${ruttingLabels[ruttingDepthBobot]}</span>
                            <small class="text-muted ml-2">(bobot ${ruttingDepthBobot})</small>
                        </td>
                    </tr>
                </table>

                <div class="alert alert-danger mb-3" style="background: #ffebee;">
                    <i class="fas fa-calculator"></i> ${step4.explanation || '-'}
                    ${step4.addition ? `<br><strong>Penambahan: +${smartFormat(step4.addition, 'decimal2')}</strong>` : ''}
                </div>

                <div class="text-center p-3" style="background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); border-radius: 8px;">
                    <h4 class="mb-0" style="color: white;">
                        <i class="fas fa-flag-checkered"></i> HASIL SDI4 = <strong>${smartFormat(step4.value, 'decimal2')}</strong>
                    </h4>
                </div>
            </div>
        </div>
    `;
}

// ========================================
// HELPER: BUILD FINAL RESULT HTML
// ========================================
function buildFinalResultHTML(sdiFinal, category, badgeClass, customStyle, badgeIcon) {
    return `
        <div class="card border-0 mb-0" style="box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
            <div class="card-header text-center py-3" style="background: linear-gradient(135deg, #333 0%, #555 100%); color: white;">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> HASIL AKHIR</h5>
            </div>
            <div class="card-body text-center p-4" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                <h1 class="display-3 mb-3" style="color: #667eea; font-weight: bold;">${smartFormat(sdiFinal, 'decimal2')}</h1>
                <span class="badge ${badgeClass}" style="font-size: 20px; padding: 12px 30px; ${customStyle}">
                    <i class="fas ${badgeIcon}"></i> ${category}
                </span>
                
                <hr style="margin: 25px 0;">
                
                <div class="text-left" style="background: white; padding: 20px; border-radius: 8px;">
                    <h6 class="mb-3"><i class="fas fa-info-circle"></i> <strong>Referensi:</strong></h6>
                    <table class="table table-sm table-bordered mb-0">
                        <tr style="background: #c8e6c9;">
                            <td width="50%"><i class="fas fa-check-circle text-success"></i> <strong>Baik</strong></td>
                            <td>SDI < 50</td>
                        </tr>
                        <tr style="background: #fff9c4;">
                            <td><i class="fas fa-exclamation-circle" style="color: #FFD700;"></i> <strong>Sedang</strong></td>
                            <td>50 ≤ SDI < 100</td>
                        </tr>
                        <tr style="background: #ffe0b2;">
                            <td><i class="fas fa-times-circle" style="color: #FFA500;"></i> <strong>Rusak Ringan</strong></td>
                            <td>100 ≤ SDI < 150</td>
                        </tr>
                        <tr style="background: #ffcdd2;">
                            <td><i class="fas fa-ban text-danger"></i> <strong>Rusak Berat</strong></td>
                            <td>SDI ≥ 150</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    `;
}

// ========================================
// EXPORT TABLE TO EXCEL
// ========================================
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

console.log('✅ Show Blade with RAW DATA columns loaded successfully');
</script>

<style>
.badge-pavement {
    transition: all 0.3s ease;
}

.badge-pavement:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.badge .fa-exclamation-triangle {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

td.pavement-cell {
    min-width: 120px;
}

.btn-secondary:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

.sdi-detail-modal .swal2-popup {
    font-size: 14px;
}

.sdi-detail-modal .table {
    font-size: 13px;
}

.sdi-detail-modal .table td {
    padding: 0.5rem;
}

.badge-lg {
    padding: 0.5rem 1rem;
    font-size: 14px;
}
</style>
@endpush