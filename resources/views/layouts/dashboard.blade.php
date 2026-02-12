@extends('layouts.template')

@section('content')

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        
        {{-- Alert Messages --}}
        @if(isset($info))
        <div class="alert alert-info alert-dismissible show fade">
            <div class="alert-body">
                <button class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
                <i class="fas fa-info-circle"></i> {{ $info }}
                <br><br>
                <strong>Saran:</strong>
                <ul class="mb-0 mt-2">
                    <li>Tambahkan data baru untuk kondisi jalan</li>
                    <li>Atau pilih tahun lain di filter yang tersedia</li>
                </ul>
            </div>
        </div>
        @endif
        
        @if(isset($error))
        <div class="alert alert-danger alert-dismissible show fade">
            <div class="alert-body">
                <button class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
                <i class="fas fa-exclamation-circle"></i> {{ $error }}
            </div>
        </div>
        @endif
        
        {{-- ROW 1: STATISTIK UTAMA - 4 CARD RAPI --}}
        <div class="row">
            
            {{-- Card 1: Total Segmen dengan Breakdown Kondisi --}}
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card card-statistic-2" style="min-height: 260px;">
                    <div class="card-icon shadow-primary bg-primary">
                        <i class="fas fa-road"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header pb-0">
                            <h4 class="mb-0" style="font-size: 13px; font-weight: 500;">Total Segmen</h4>
                        </div>
                        <div class="card-body pt-2 pb-1">
                            <h2 class="mb-1" style="font-size: 26px; font-weight: 700; line-height: 1.2;">{{ number_format($totalSegments ?? 0) }}</h2>
                            <div class="text-muted" style="font-size: 11px;">
                                @if(isset($selectedYear))
                                    <i class="fas fa-calendar-alt"></i> Tahun {{ $selectedYear }}
                                @else
                                    Semua Tahun
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2 pb-2">
                        <div class="row text-center mb-2">
                            <div class="col-6 border-right">
                                <div class="font-weight-bold text-success" style="font-size: 20px; line-height: 1;">{{ $goodCondition ?? 0 }}</div>
                                <div class="text-muted mt-1" style="font-size: 11px;">Baik</div>
                            </div>
                            <div class="col-6">
                                <div class="font-weight-bold" style="font-size: 20px; color: #FFD700; line-height: 1;">{{ $fairCondition ?? 0 }}</div>
                                <div class="text-muted mt-1" style="font-size: 11px;">Sedang</div>
                            </div>
                        </div>
                        <div class="row text-center pt-2 border-top">
                            <div class="col-6 border-right">
                                <div class="font-weight-bold text-warning" style="font-size: 20px; line-height: 1;">{{ $lightDamage ?? 0 }}</div>
                                <div class="text-muted mt-1" style="font-size: 11px;">Rusak Ringan</div>
                            </div>
                            <div class="col-6">
                                <div class="font-weight-bold text-danger" style="font-size: 20px; line-height: 1;">{{ $heavyDamage ?? 0 }}</div>
                                <div class="text-muted mt-1" style="font-size: 11px;">Rusak Berat</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Card 2: Rata-rata SDI --}}
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card card-statistic-2" style="min-height: 260px;">
                    <div class="card-chart">
                        <canvas id="sdi-chart" height="60"></canvas>
                    </div>
                    <div class="card-icon shadow-primary 
                        @if(($avgSDI ?? 0) < 50) bg-success
                        @elseif(($avgSDI ?? 0) < 100) bg-warning
                        @elseif(($avgSDI ?? 0) < 150) bg-danger
                        @else bg-danger
                        @endif">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header pb-0">
                            <h4 class="mb-0" style="font-size: 13px; font-weight: 500;">Rata-rata SDI</h4>
                        </div>
                        <div class="card-body pt-2">
                            <h2 class="mb-2" style="font-size: 26px; font-weight: 700; line-height: 1.2;">{{ number_format($avgSDI ?? 0, 2) }}</h2>
                            <div style="font-size: 11px;">
                                @php
                                    $avgSDICategory = 'Baik';
                                    if (($avgSDI ?? 0) >= 150) {
                                        $avgSDICategory = 'Rusak Berat';
                                    } elseif (($avgSDI ?? 0) >= 100) {
                                        $avgSDICategory = 'Rusak Ringan';
                                    } elseif (($avgSDI ?? 0) >= 50) {
                                        $avgSDICategory = 'Sedang';
                                    }
                                @endphp
                                <x-sdi-badge :category="$avgSDICategory" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Card 3: Total Panjang Jalan --}}
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card card-statistic-2" style="min-height: 260px;">
                    <div class="card-chart">
                        <canvas id="length-chart" height="60"></canvas>
                    </div>
                    <div class="card-icon shadow-primary bg-primary">
                        <i class="fas fa-route"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header pb-0">
                            <h4 class="mb-0" style="font-size: 13px; font-weight: 500;">Panjang Jalan</h4>
                        </div>
                        <div class="card-body pt-2">
                            <h2 class="mb-2" style="font-size: 22px; font-weight: 700; line-height: 1.2;">{{ number_format($totalLength ) }}</h2>
                            <div class="text-muted" style="font-size: 11px;">kilometer</div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Card 4: Kerusakan Kritis --}}
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card card-statistic-2" style="min-height: 260px;">
                    <div class="card-icon shadow-danger bg-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header pb-0">
                            <h4 class="mb-0" style="font-size: 13px; font-weight: 500;">Kerusakan Kritis</h4>
                        </div>
                        <div class="card-body pt-2 pb-1">
                            <h2 class="mb-1" style="font-size: 26px; font-weight: 700; line-height: 1.2;">{{ number_format($criticalSegments ?? 0) }}</h2>
                            <div class="text-muted" style="font-size: 11px;">Segmen Prioritas</div>
                        </div>
                    </div>
                    <div class="card-body pt-2 pb-2">
                        <div class="row text-center">
                            <div class="col-6 border-right">
                                <div class="font-weight-bold" style="font-size: 20px; line-height: 1;">{{ number_format($totalPotholes ?? 0) }}</div>
                                <div class="text-muted mt-1" style="font-size: 11px;">Lubang</div>
                            </div>
                            <div class="col-6">
                                <div class="font-weight-bold" style="font-size: 20px; line-height: 1;">{{ number_format($totalCrackArea ?? 0, 0) }}</div>
                                <div class="text-muted mt-1" style="font-size: 11px;">m² Retak</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- ROW 2: CHARTS - HEIGHT YANG SAMA --}}
        <div class="row">
            
            {{-- Chart: Trend SDI per Tahun --}}
            <div class="col-lg-6">
                <div class="card" style="height: 400px;">
                    <div class="card-header">
                        <h4>Trend SDI per Tahun</h4>
                        <div class="card-header-action">
                            <div class="badge badge-info">
                                @if(isset($selectedYear))
                                    Filter: Tahun {{ $selectedYear }}
                                @else
                                    Semua Data
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="height: calc(100% - 60px);">
                        @if(($sdiByYear ?? collect())->isEmpty())
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-chart-line fa-3x mb-3"></i>
                                <p>Tidak ada data trend SDI</p>
                            </div>
                        @else
                            <canvas id="sdiTrendChart"></canvas>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- Distribusi Kondisi Jalan --}}
            <div class="col-lg-6">
                <div class="card" style="height: 400px;">
                    <div class="card-header">
                        <h4>Distribusi Kondisi Jalan</h4>
                    </div>
                    <div class="card-body" style="height: calc(100% - 60px);">
                        @if(($totalSegments ?? 0) > 0)
                            <div class="row h-100">
                                <div class="col-md-6 d-flex align-items-center justify-content-center">
                                    <canvas id="conditionDistChart" style="max-height: 250px;"></canvas>
                                </div>
                                <div class="col-md-6 d-flex flex-column justify-content-center">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span><i class="fas fa-circle text-success"></i> Baik</span>
                                            <strong class="text-success">{{ number_format($percentGood ?? 0, 1) }}%</strong>
                                        </div>
                                        <small class="text-muted">{{ number_format($lengthByCategory['baik'] ?? 0, 2) }} km</small>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span><i class="fas fa-circle" style="color: #FFD700;"></i> Sedang</span>
                                            <strong style="color: #FFD700;">{{ number_format($percentFair ?? 0, 1) }}%</strong>
                                        </div>
                                        <small class="text-muted">{{ number_format($lengthByCategory['sedang'] ?? 0, 2) }} km</small>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span><i class="fas fa-circle text-warning"></i> Rusak Ringan</span>
                                            <strong class="text-warning">{{ number_format($percentLight ?? 0, 1) }}%</strong>
                                        </div>
                                        <small class="text-muted">{{ number_format($lengthByCategory['rusak_ringan'] ?? 0, 2) }} km</small>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span><i class="fas fa-circle text-danger"></i> Rusak Berat</span>
                                            <strong class="text-danger">{{ number_format($percentHeavy ?? 0, 1) }}%</strong>
                                        </div>
                                        <small class="text-muted">{{ number_format($lengthByCategory['rusak_berat'] ?? 0, 2) }} km</small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-chart-pie fa-3x mb-3"></i>
                                <p>Tidak ada data distribusi</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 2.5: GRAFIK KONDISI JALAN PER TAHUN --}}
        <div class="row">
            <div class="col-12">
                <div class="card" style="height: 500px;">
                    <div class="card-header">
                        <h4><i class="fas fa-chart-bar"></i> Statistik Kondisi Jalan per Tahun</h4>
                        <div class="card-header-action">
                            <div class="badge badge-primary">
                                Total {{ ($conditionByYear ?? collect())->count() }} Tahun
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="height: calc(100% - 60px); padding: 20px;">
                        @if(($conditionByYear ?? collect())->isEmpty())
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                <p>Tidak ada data kondisi jalan per tahun</p>
                            </div>
                        @else
                            <canvas id="conditionByYearChart"></canvas>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        {{-- ROW 3: TOP 5 RUAS DAN KECAMATAN - HEIGHT SAMA --}}
        <div class="row">
            
            {{-- Top 5 Ruas Terburuk --}}
            <div class="col-lg-6">
                <div class="card" style="height: 550px;">
                    <div class="card-header">
                        <h4>Top 5 Ruas Terburuk</h4>
                        <div class="card-header-action">
                            <span class="badge badge-danger">Prioritas Tinggi</span>
                        </div>
                    </div>
                    <div class="card-body p-0" style="height: calc(100% - 110px); overflow-y: auto;">
                        <div class="table-responsive">
                            <table class="table table-striped table-md mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Ruas</th>
                                        <th class="text-center">SDI</th>
                                        <th class="text-center">Kondisi</th>
                                        <th class="text-right">Panjang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($worstLinks ?? [] as $index => $link)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="font-weight-600">{{ $link['link_name'] }}</div>
                                            <div class="text-small text-muted">{{ $link['link_code'] }}</div>
                                            @if(isset($link['province']))
                                            <div class="text-small text-muted">
                                                <i class="fas fa-map-marker-alt"></i> {{ $link['province'] }}
                                            </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <x-sdi-badge 
                                                category="Rusak Berat" 
                                                :sdi-value="$link['avg_sdi']" 
                                            />
                                        </td>
                                        <td class="text-center">
                                            <x-sdi-badge :category="$link['category']" />
                                        </td>
                                        <td class="text-right">{{ number_format($link['total_length'], 2) }} km</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>Tidak ada data</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if(count($worstLinks ?? []) > 0)
                    <div class="card-footer text-center">
                        <a href="{{ route('kondisi-jalan.index') }}" class="btn btn-primary btn-sm">
                            Lihat Semua Ruas <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            
            {{-- Top 10 Kecamatan --}}
            <div class="col-lg-6">
                <div class="card" style="height: 550px;">
                    <div class="card-header">
                        <h4><i class="fas fa-map-marked-alt"></i> Top 10 Kecamatan (SDI Tertinggi)</h4>
                        <div class="card-header-action">
                            <span class="badge badge-info">Kabupaten Jember</span>
                        </div>
                    </div>
                    <div class="card-body" style="height: calc(100% - 60px); overflow-y: auto;">
                        @forelse($kecamatanStats ?? [] as $index => $kecamatan)
                        <div class="mb-3 pb-3 {{ $loop->last ? '' : 'border-bottom' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="font-weight-600">{{ $index + 1 }}. {{ $kecamatan['kecamatan_name'] }}</div>
                                    <small class="text-muted">
                                        {{ $kecamatan['total_links'] }} ruas • {{ number_format($kecamatan['total_length'], 2) }} km
                                    </small>
                                </div>
                                <x-sdi-badge 
                                    :category="$kecamatan['category']" 
                                    :sdi-value="$kecamatan['avg_sdi']" 
                                    class="badge-lg"
                                />
                            </div>
                            @php
                                $total = $kecamatan['good'] + $kecamatan['fair'] + $kecamatan['light'] + $kecamatan['heavy'];
                                $total = $total > 0 ? $total : 1;
                            @endphp
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ ($kecamatan['good'] / $total) * 100 }}%" 
                                     title="Baik: {{ $kecamatan['good'] }}"></div>
                                <div class="progress-bar bg-warning" style="width: {{ ($kecamatan['fair'] / $total) * 100 }}%"
                                     title="Sedang: {{ $kecamatan['fair'] }}"></div>
                                <div class="progress-bar bg-danger" style="width: {{ ($kecamatan['light'] / $total) * 100 }}%"
                                     title="Rusak Ringan: {{ $kecamatan['light'] }}"></div>
                                <div class="progress-bar bg-danger" style="width: {{ ($kecamatan['heavy'] / $total) * 100 }}%"
                                     title="Rusak Berat: {{ $kecamatan['heavy'] }}"></div>
                            </div>
                            <div class="small">
                                <span class="text-success mr-2"><i class="fas fa-square"></i> {{ $kecamatan['good'] }}</span>
                                <span class="text-warning mr-2"><i class="fas fa-square"></i> {{ $kecamatan['fair'] }}</span>
                                <span class="text-danger mr-2"><i class="fas fa-square"></i> {{ $kecamatan['light'] }}</span>
                                <span class="text-dark"><i class="fas fa-square"></i> {{ $kecamatan['heavy'] }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-map-marked-alt fa-2x mb-2"></i>
                            <p>Tidak ada data kecamatan</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        {{-- ROW 4: STATISTIK KERUSAKAN DAN DATA TERBARU - HEIGHT SAMA --}}
        <div class="row">
            
            {{-- Statistik Kerusakan Detail --}}
            <div class="col-lg-8">
                <div class="card" style="height: 450px;">
                    <div class="card-header">
                        <h4>Statistik Kerusakan Detail</h4>
                        <div class="card-header-action">
                            <a href="{{ route('kondisi-jalan.index') }}" class="btn btn-primary btn-sm">
                                Lihat Detail <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0" style="height: calc(100% - 60px);">
                        <div class="table-responsive">
                            <table class="table table-striped table-md mb-0">
                                <thead>
                                    <tr>
                                        <th>Jenis Kerusakan</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-center">Satuan</th>
                                        <th class="text-center">Prioritas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <i class="fas fa-square text-danger mr-2"></i>
                                            <strong>Luas Retak</strong>
                                        </td>
                                        <td class="text-right font-weight-600">
                                            {{ number_format($totalCrackArea ?? 0, 2) }}
                                        </td>
                                        <td class="text-center">m²</td>
                                        <td class="text-center">
                                            <span class="badge badge-danger">Kritis</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <i class="fas fa-circle text-warning mr-2"></i>
                                            <strong>Lubang (Pothole)</strong>
                                        </td>
                                        <td class="text-right font-weight-600">
                                            {{ number_format($totalPotholes ?? 0) }}
                                        </td>
                                        <td class="text-center">buah</td>
                                        <td class="text-center">
                                            <span class="badge badge-warning">Tinggi</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <i class="fas fa-square text-danger mr-2"></i>
                                            <strong>Luas Lubang</strong>
                                        </td>
                                        <td class="text-right font-weight-600">
                                            {{ number_format($totalPotholeArea ?? 0, 2) }}
                                        </td>
                                        <td class="text-center">m²</td>
                                        <td class="text-center">
                                            <span class="badge badge-danger">Kritis</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <i class="fas fa-square text-info mr-2"></i>
                                            <strong>Luas Alur Roda</strong>
                                        </td>
                                        <td class="text-right font-weight-600">
                                            {{ number_format($totalRuttingArea ?? 0, 2) }}
                                        </td>
                                        <td class="text-center">m²</td>
                                        <td class="text-center">
                                            <span class="badge badge-info">Sedang</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <i class="fas fa-square text-secondary mr-2"></i>
                                            <strong>Luas Patching</strong>
                                        </td>
                                        <td class="text-right font-weight-600">
                                            {{ number_format($totalPatchingArea ?? 0, 2) }}
                                        </td>
                                        <td class="text-center">m²</td>
                                        <td class="text-center">
                                            <span class="badge badge-secondary">Monitor</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Data Update Terbaru --}}
            <div class="col-lg-4">
                <div class="card" style="height: 450px;">
                    <div class="card-header">
                        <h4><i class="fas fa-clock"></i> Update Terbaru</h4>
                        <div class="card-header-action">
                            <span class="badge badge-primary">{{ count($recentUpdates ?? []) }} data</span>
                        </div>
                    </div>
                    <div class="card-body p-0" style="height: calc(100% - 60px); overflow-y: auto;">
                        <ul class="list-unstyled list-unstyled-border mb-0">
                            @forelse($recentUpdates ?? [] as $update)
                            <li class="media p-3">
                                <div class="media-body">
                                    <div class="media-title font-weight-600 mb-1">
                                        {{ $update['link_name'] }}
                                    </div>
                                    <div class="mb-2">
                                        <x-sdi-badge 
                                            :category="$update['category']" 
                                            :sdi-value="$update['sdi_value']"
                                        />
                                    </div>
                                    <div class="text-small text-muted">
                                        <div><i class="fas fa-road"></i> Km {{ $update['chainage_from'] }} - {{ $update['chainage_to'] }}</div>
                                        <div><i class="fas fa-calendar"></i> Tahun {{ $update['year'] }} • {{ $update['updated_at'] }}</div>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="text-center text-muted p-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">Tidak ada data terbaru</p>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
    </section>
</div>

@endsection

@section('page-script')
{{-- Override default index.js --}}
@endsection

@push('scripts')
<script>
// Destroy any existing Chart instances
if (typeof Chart !== 'undefined') {
    Object.keys(Chart.instances || {}).forEach(key => {
        if (Chart.instances[key]) {
            Chart.instances[key].destroy();
        }
    });
}
</script>

<!-- CDN Chart.js v3 -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
window.addEventListener('load', function() {
    console.log('🚀 Initializing Dashboard Charts...');
    console.log('Chart.js version:', Chart.version);

    // 🎨 DEFINISI WARNA KONSISTEN UNTUK SEMUA CHART
    const SDI_COLORS = {
    baik: {
        bg: 'rgba(72, 187, 120, 0.8)',
        border: 'rgba(72, 187, 120, 1)'
    },
    sedang: {
        bg: 'rgba(255, 215, 0, 0.8)',
        border: 'rgba(255, 215, 0, 1)'
    },
    rusakRingan: {
        bg: 'rgba(255, 193, 7, 0.8)',
        border: 'rgba(255, 193, 7, 1)'
    },
    rusakBerat: {
        bg: 'rgba(220, 53, 69, 0.8)',
        border: 'rgba(220, 53, 69, 1)'
    }
};

    // Helper function
    function createChartSafely(canvasId, config) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) {
            console.warn(`Canvas ${canvasId} not found`);
            return null;
        }

        try {
            const ctx = canvas.getContext('2d');
            if (!ctx) {
                console.error(`Cannot get context for ${canvasId}`);
                return null;
            }

            const existingChart = Chart.getChart(canvas);
            if (existingChart) {
                existingChart.destroy();
            }

            const chart = new Chart(ctx, config);
            console.log(`✓ ${canvasId} created successfully`);
            return chart;
        } catch (error) {
            console.error(`✗ Error creating ${canvasId}:`, error);
            return null;
        }
    }

    // Configure Chart.js defaults
    Chart.defaults.font.family = "'Nunito', 'Segoe UI', 'Arial'";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6c757d';

    // Data dari backend
    const sdiByYearData = {!! json_encode($sdiByYear ?? collect()) !!};
    const years = sdiByYearData.map(item => item.year);
    const avgSdiValues = sdiByYearData.map(item => item.avg_sdi);
    const minSdiValues = sdiByYearData.map(item => item.min_sdi);
    const maxSdiValues = sdiByYearData.map(item => item.max_sdi);
    const counts = sdiByYearData.map(item => item.count);

    // Data kondisi per tahun
    const conditionByYearData = {!! json_encode($conditionByYear ?? collect()) !!};
    const conditionYears = conditionByYearData.map(item => item.year);
    const baikData = conditionByYearData.map(item => item.baik);
    const sedangData = conditionByYearData.map(item => item.sedang);
    const rusakRinganData = conditionByYearData.map(item => item.rusak_ringan);
    const rusakBeratData = conditionByYearData.map(item => item.rusak_berat);

    const hasData = years.length > 0;
    const hasConditionData = conditionYears.length > 0;

    if (hasData) {
        // 1. SDI Mini Chart
        createChartSafely('sdi-chart', {
            type: 'line',
            data: {
                labels: years,
                datasets: [{
                    label: 'SDI',
                    data: avgSdiValues,
                    borderWidth: 2,
                    backgroundColor: 'rgba(63,82,227,.8)',
                    borderColor: 'rgba(63,82,227,1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(63,82,227,1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { display: false },
                    x: { display: false }
                }
            }
        });

        // 2. Length Mini Chart
        createChartSafely('length-chart', {
            type: 'line',
            data: {
                labels: years,
                datasets: [{
                    label: 'Segmen',
                    data: counts,
                    borderWidth: 2,
                    backgroundColor: 'rgba(63,82,227,.8)',
                    borderColor: 'rgba(63,82,227,1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(63,82,227,1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { display: false },
                    x: { display: false }
                }
            }
        });

        // 3. SDI Trend Chart
        createChartSafely('sdiTrendChart', {
            type: 'bar',
            data: {
                labels: years,
                datasets: [
                    {
                        label: 'Rata-rata SDI',
                        data: avgSdiValues,
                        backgroundColor: 'rgba(63, 82, 227, 0.8)',
                        borderColor: 'rgba(63, 82, 227, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'SDI Minimal',
                        data: minSdiValues,
                        type: 'line',
                        borderColor: SDI_COLORS.baik.border,
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        pointRadius: 3,
                        tension: 0.4
                    },
                    {
                        label: 'SDI Maksimal',
                        data: maxSdiValues,
                        type: 'line',
                        borderColor: SDI_COLORS.rusakBerat.border,
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        pointRadius: 3,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nilai SDI'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tahun'
                        }
                    }
                }
            }
        });
    }

    // 4. Condition Distribution Chart
    const totalSegments = {{ $totalSegments ?? 0 }};
    if (totalSegments > 0) {
        createChartSafely('conditionDistChart', {
            type: 'doughnut',
            data: {
                labels: ['Baik', 'Sedang', 'Rusak Ringan', 'Rusak Berat'],
                datasets: [{
                    data: [
                        {{ $goodCondition ?? 0 }},
                        {{ $fairCondition ?? 0 }},
                        {{ $lightDamage ?? 0 }},
                        {{ $heavyDamage ?? 0 }}
                    ],
                    backgroundColor: [
                        SDI_COLORS.baik.bg,
                        SDI_COLORS.sedang.bg,
                        SDI_COLORS.rusakRingan.bg,
                        SDI_COLORS.rusakBerat.bg
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // 5. Condition by Year Chart
    if (hasConditionData) {
        createChartSafely('conditionByYearChart', {
            type: 'bar',
            data: {
                labels: conditionYears,
                datasets: [
                    {
                        label: 'Baik',
                        data: baikData,
                        backgroundColor: SDI_COLORS.baik.bg,
                        borderColor: SDI_COLORS.baik.border,
                        borderWidth: 1
                    },
                    {
                        label: 'Sedang',
                        data: sedangData,
                        backgroundColor: SDI_COLORS.sedang.bg,
                        borderColor: SDI_COLORS.sedang.border,
                        borderWidth: 1
                    },
                    {
                        label: 'Rusak Ringan',
                        data: rusakRinganData,
                        backgroundColor: SDI_COLORS.rusakRingan.bg,
                        borderColor: SDI_COLORS.rusakRingan.border,
                        borderWidth: 1
                    },
                    {
                        label: 'Rusak Berat',
                        data: rusakBeratData,
                        backgroundColor: SDI_COLORS.rusakBerat.bg,
                        borderColor: SDI_COLORS.rusakBerat.border,
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 10,
                        bottom: 10
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'center',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: { size: 12 },
                            boxWidth: 12,
                            boxHeight: 12
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            footer: function(tooltipItems) {
                                let total = 0;
                                tooltipItems.forEach(function(tooltipItem) {
                                    total += tooltipItem.parsed.y;
                                });
                                return 'Total: ' + total + ' segmen';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        title: {
                            display: true,
                            text: 'Tahun',
                            font: { size: 13, weight: 'bold' },
                            padding: { top: 10 }
                        },
                        grid: { display: false },
                        ticks: { font: { size: 12 } }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Segmen',
                            font: { size: 13, weight: 'bold' }
                        },
                        ticks: {
                            precision: 0,
                            font: { size: 12 }
                        },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    }
                }
            }
        });
    }

    console.log('✅ All dashboard charts initialized');
});
</script>
@endpush