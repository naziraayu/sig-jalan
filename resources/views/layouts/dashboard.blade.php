@extends('layouts.template')

@section('content')

<div class="main-content">
    <section class="section">

        {{-- ===== GLOBAL DASHBOARD STYLES ===== --}}
        <style>
            /* === DESIGN SYSTEM: SEMUA CARD PAKAI INI === */
            .db-card {
                background: #fff;
                border-radius: 14px;
                box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.07);
                overflow: hidden;
                display: flex;
                flex-direction: column;
                height: 100%;
                transition: box-shadow .2s ease;
            }
            .db-card:hover {
                box-shadow: 0 4px 8px rgba(0,0,0,.08), 0 8px 28px rgba(0,0,0,.10);
            }
            /* Header semua card */
            .db-card-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px 20px;
                border-bottom: 1px solid #f1f5f9;
                flex-shrink: 0;
            }
            .db-card-header h4 {
                font-size: 14px;
                font-weight: 700;
                color: #1e293b;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 7px;
            }
            .db-card-header h4 i {
                color: #94a3b8;
                font-size: 13px;
            }
            /* Body semua card */
            .db-card-body {
                padding: 20px;
                flex: 1;
            }
            .db-card-body.no-pad {
                padding: 0;
            }
            .db-card-body.overflow-y {
                overflow-y: auto;
            }
            /* Footer semua card */
            .db-card-footer {
                padding: 12px 20px;
                border-top: 1px solid #f1f5f9;
                flex-shrink: 0;
            }

            /* === STAT CARDS (ROW 1) === */
            .stat-icon {
                width: 50px;
                height: 50px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                color: #fff;
                flex-shrink: 0;
            }
            .stat-label {
                font-size: 11px;
                font-weight: 600;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.6px;
                margin-bottom: 4px;
            }
            .stat-value {
                font-size: 30px;
                font-weight: 800;
                color: #0f172a;
                line-height: 1;
            }
            .stat-sub {
                font-size: 11px;
                color: #94a3b8;
                margin-top: 5px;
            }
            .stat-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                padding: 18px 20px 14px;
                flex-shrink: 0;
            }
            .stat-divider {
                height: 1px;
                background: #f1f5f9;
                margin: 0;
            }
            .stat-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                padding: 14px 20px 18px;
            }
            .stat-grid-item {
                background: #f8fafc;
                border-radius: 8px;
                padding: 10px 8px;
                text-align: center;
            }
            .stat-grid-item.full {
                grid-column: span 2;
            }
            .stat-grid-val {
                font-size: 18px;
                font-weight: 700;
                line-height: 1;
            }
            .stat-grid-lbl {
                font-size: 10px;
                color: #94a3b8;
                margin-top: 4px;
                font-weight: 500;
            }
            .mini-chart-wrap {
                padding: 10px 20px 18px;
                flex: 1;
            }

            /* === BADGE COLORS === */
            .badge-db {
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }
            .badge-db.blue   { background: #eff6ff; color: #2563eb; }
            .badge-db.red    { background: #fef2f2; color: #dc2626; }
            .badge-db.green  { background: #f0fdf4; color: #16a34a; }
            .badge-db.yellow { background: #fefce8; color: #ca8a04; }
            .badge-db.purple { background: #f5f3ff; color: #7c3aed; }
            .badge-db.gray   { background: #f8fafc; color: #64748b; }

            /* === TABLE INSIDE CARD === */
            .db-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 13px;
            }
            .db-table thead th {
                background: #f8fafc;
                color: #64748b;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                padding: 10px 16px;
                border-bottom: 1px solid #e2e8f0;
            }
            .db-table tbody td {
                padding: 12px 16px;
                border-bottom: 1px solid #f1f5f9;
                color: #334155;
                vertical-align: middle;
            }
            .db-table tbody tr:last-child td { border-bottom: none; }
            .db-table tbody tr:hover { background: #f8fafc; }
            .db-table .rank-num {
                width: 28px;
                height: 28px;
                border-radius: 8px;
                background: #f1f5f9;
                color: #64748b;
                font-weight: 700;
                font-size: 12px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            .db-table .road-name { font-weight: 600; color: #0f172a; font-size: 13px; }
            .db-table .road-code { font-size: 11px; color: #94a3b8; margin-top: 2px; }

            /* === KECAMATAN LIST === */
            .kec-item {
                padding: 14px 20px;
                border-bottom: 1px solid #f1f5f9;
            }
            .kec-item:last-child { border-bottom: none; }
            .kec-item:hover { background: #f8fafc; }
            .kec-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                margin-bottom: 8px;
            }
            .kec-name {
                font-weight: 700;
                color: #0f172a;
                font-size: 13px;
            }
            .kec-meta {
                font-size: 11px;
                color: #94a3b8;
                margin-top: 2px;
            }
            .kec-progress {
                height: 6px;
                border-radius: 6px;
                background: #e2e8f0;
                overflow: hidden;
                display: flex;
                margin-bottom: 7px;
            }
            .kec-dots {
                display: flex;
                gap: 10px;
                font-size: 11px;
                flex-wrap: wrap;
            }
            .kec-dot { display: flex; align-items: center; gap: 4px; }
            .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; flex-shrink: 0; }

            /* === RECENT UPDATE LIST === */
            .update-item {
                padding: 14px 20px;
                border-bottom: 1px solid #f1f5f9;
            }
            .update-item:last-child { border-bottom: none; }
            .update-item:hover { background: #f8fafc; }
            .update-name {
                font-weight: 700;
                color: #0f172a;
                font-size: 13px;
                margin-bottom: 5px;
            }
            .update-meta {
                font-size: 11px;
                color: #94a3b8;
                margin-top: 5px;
            }
            .update-meta i { width: 12px; }

            /* === EMPTY STATE === */
            .empty-state {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 48px 20px;
                color: #94a3b8;
            }
            .empty-state i { font-size: 36px; margin-bottom: 12px; opacity: .5; }
            .empty-state p { font-size: 13px; margin: 0; }

            /* === CHART CONTAINERS === */
            .chart-wrap {
                position: relative;
                width: 100%;
                height: 100%;
            }

            /* === ALERTS === */
            .db-alert {
                border-radius: 12px;
                padding: 16px 20px;
                margin-bottom: 20px;
                display: flex;
                align-items: flex-start;
                gap: 12px;
                font-size: 13px;
            }
            .db-alert.info    { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
            .db-alert.danger  { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
            .db-alert i { margin-top: 1px; flex-shrink: 0; }
            .db-alert-close {
                margin-left: auto;
                background: none;
                border: none;
                cursor: pointer;
                color: inherit;
                opacity: .6;
                padding: 0;
                line-height: 1;
            }

            /* === RESPONSIVE === */
            @media (max-width: 991px) {
                .stat-value { font-size: 26px; }
            }
            @media (max-width: 767px) {
                .stat-value { font-size: 24px; }
                .stat-grid-val { font-size: 16px; }
                .mb-row { margin-bottom: 16px; }
            }
            @media (max-width: 575px) {
                .db-card-header { flex-wrap: wrap; gap: 8px; }
            }
        </style>

        {{-- ===== ALERT MESSAGES ===== --}}
        @if(isset($info))
        <div class="db-alert info" role="alert">
            <i class="fas fa-info-circle"></i>
            <div>
                {{ $info }}
                <ul style="margin: 8px 0 0; padding-left: 18px;">
                    <li>Tambahkan data baru untuk kondisi jalan</li>
                    <li>Atau pilih tahun lain di filter yang tersedia</li>
                </ul>
            </div>
            <button class="db-alert-close" onclick="this.closest('.db-alert').remove()"><i class="fas fa-times"></i></button>
        </div>
        @endif
        @if(isset($error))
        <div class="db-alert danger" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <div>{{ $error }}</div>
            <button class="db-alert-close" onclick="this.closest('.db-alert').remove()"><i class="fas fa-times"></i></button>
        </div>
        @endif

        {{-- ===== ROW 1: STAT CARDS ===== --}}
        <div class="row mb-2">

            {{-- Card 1: Total Segmen --}}
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                <div class="db-card">
                    <div class="stat-top">
                        <div>
                            <div class="stat-label">Total Segmen</div>
                            <div class="stat-value">{{ number_format($totalSegments ?? 0) }}</div>
                            <div class="stat-sub">
                                @if(isset($selectedYear))
                                    <i class="fas fa-calendar-alt"></i> Tahun {{ $selectedYear }}
                                @else
                                    <i class="fas fa-layer-group"></i> Semua Tahun
                                @endif
                            </div>
                        </div>
                        <div class="stat-icon" style="background: linear-gradient(135deg,#3b82f6,#1d4ed8);">
                            <i class="fas fa-road"></i>
                        </div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-grid">
                        <div class="stat-grid-item">
                            <div class="stat-grid-val text-success">{{ $goodCondition ?? 0 }}</div>
                            <div class="stat-grid-lbl">Baik</div>
                        </div>
                        <div class="stat-grid-item">
                            <div class="stat-grid-val" style="color:#FFD700">{{ $fairCondition ?? 0 }}</div>
                            <div class="stat-grid-lbl">Sedang</div>
                        </div>
                        <div class="stat-grid-item">
                            <div class="stat-grid-val" style="color:#FF8C00">{{ $lightDamage ?? 0 }}</div>
                            <div class="stat-grid-lbl">Rusak Ringan</div>
                        </div>
                        <div class="stat-grid-item">
                            <div class="stat-grid-val text-danger">{{ $heavyDamage ?? 0 }}</div>
                            <div class="stat-grid-lbl">Rusak Berat</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Rata-rata SDI --}}
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                <div class="db-card">
                    <div class="stat-top">
                        <div>
                            <div class="stat-label">Rata-rata SDI</div>
                            <div class="stat-value">{{ number_format($avgSDI ?? 0, 2) }}</div>
                            <div class="stat-sub mt-2">
                                @php
                                    $avgSDICategory = 'Baik';
                                    if (($avgSDI ?? 0) >= 150) $avgSDICategory = 'Rusak Berat';
                                    elseif (($avgSDI ?? 0) >= 100) $avgSDICategory = 'Rusak Ringan';
                                    elseif (($avgSDI ?? 0) >= 50) $avgSDICategory = 'Sedang';
                                @endphp
                                <x-sdi-badge :category="$avgSDICategory" />
                            </div>
                        </div>
                        <div class="stat-icon" style="background: linear-gradient(135deg,
                            {{ ($avgSDI ?? 0) < 50 ? '#2ecc71,#27ae60' : (($avgSDI ?? 0) < 100 ? '#FFD700,#e6c200' : (($avgSDI ?? 0) < 150 ? '#FF8C00,#e07b00' : '#e74c3c,#c0392b')) }});">

                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="mini-chart-wrap">
                        <canvas id="sdi-chart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Card 3: Panjang Jalan --}}
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                <div class="db-card">
                    <div class="stat-top">
                        <div>
                            <div class="stat-label">Panjang Jalan</div>
                            <div class="stat-value">{{ number_format($totalLength) }}</div>
                            <div class="stat-sub">kilometer total</div>
                        </div>
                        <div class="stat-icon" style="background: linear-gradient(135deg,#8b5cf6,#6d28d9);">
                            <i class="fas fa-route"></i>
                        </div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="mini-chart-wrap">
                        <canvas id="length-chart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Card 4: Kerusakan Kritis --}}
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                <div class="db-card">
                    <div class="stat-top">
                        <div>
                            <div class="stat-label">Kerusakan Kritis</div>
                            <div class="stat-value text-danger">{{ number_format($criticalSegments ?? 0) }}</div>
                            <div class="stat-sub">Segmen Prioritas</div>
                        </div>
                        <div class="stat-icon" style="background: linear-gradient(135deg,#ef4444,#b91c1c);">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-grid">
                        <div class="stat-grid-item">
                            <div class="stat-grid-val text-danger">{{ number_format($totalPotholes ?? 0) }}</div>
                            <div class="stat-grid-lbl">Lubang</div>
                        </div>
                        <div class="stat-grid-item">
                            <div class="stat-grid-val" style="color:#FF8C00">{{ number_format($totalCrackArea ?? 0, 0) }}</div>
                            <div class="stat-grid-lbl">m² Retak</div>
                        </div>
                        <div class="stat-grid-item full">
                            <div class="stat-grid-val text-info">{{ number_format($totalPotholeArea ?? 0, 0) }}</div>
                            <div class="stat-grid-lbl">m² Luas Lubang</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== ROW 2: CHARTS ===== --}}
        <div class="row mb-2">

            {{-- Chart: Trend SDI per Tahun --}}
            <div class="col-lg-6 col-12 mb-4">
                <div class="db-card" style="height: 400px;">
                    <div class="db-card-header">
                        <h4><i class="fas fa-chart-bar"></i> Trend SDI per Tahun</h4>
                        <span class="badge-db blue">
                            @if(isset($selectedYear))
                                <i class="fas fa-filter"></i> {{ $selectedYear }}
                            @else
                                <i class="fas fa-database"></i> Semua Data
                            @endif
                        </span>
                    </div>
                    <div class="db-card-body" style="height: calc(100% - 57px); padding: 16px 20px;">
                        @if(($sdiByYear ?? collect())->isEmpty())
                            <div class="empty-state">
                                <i class="fas fa-chart-line"></i>
                                <p>Tidak ada data trend SDI</p>
                            </div>
                        @else
                            <canvas id="sdiTrendChart" style="width:100%;height:100%;"></canvas>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Distribusi Kondisi Jalan --}}
            <div class="col-lg-6 col-12 mb-4">
                <div class="db-card" style="height: 400px;">
                    <div class="db-card-header">
                        <h4><i class="fas fa-chart-pie"></i> Distribusi Kondisi Jalan</h4>
                        <span class="badge-db purple">
                            <i class="fas fa-road"></i> {{ number_format($totalSegments ?? 0) }} segmen
                        </span>
                    </div>
                    <div class="db-card-body" style="height: calc(100% - 57px); padding: 16px 20px;">
                        @if(($totalSegments ?? 0) > 0)
                            <div class="row h-100" style="align-items:center;">
                                <div class="col-md-5 d-flex align-items-center justify-content-center">
                                    <canvas id="conditionDistChart" style="max-height:220px;"></canvas>
                                </div>
                                <div class="col-md-7 d-flex flex-column justify-content-center" style="gap:12px;">
                                    @php
                                        $distItems = [
                                            ['label'=>'Baik','pct'=>$percentGood??0,'km'=>$lengthByCategory['baik']??0,'color'=>'#2ecc71'],
                                            ['label'=>'Sedang','pct'=>$percentFair??0,'km'=>$lengthByCategory['sedang']??0,'color'=>'#FFD700'],
                                            ['label'=>'Rusak Ringan','pct'=>$percentLight??0,'km'=>$lengthByCategory['rusak_ringan']??0,'color'=>'#FF8C00'],
                                            ['label'=>'Rusak Berat','pct'=>$percentHeavy??0,'km'=>$lengthByCategory['rusak_berat']??0,'color'=>'#e74c3c'],
                                        ];
                                    @endphp
                                    @foreach($distItems as $item)
                                    <div>
                                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
                                            <span style="font-size:13px;color:#334155;font-weight:600;display:flex;align-items:center;gap:6px;">
                                                <span class="dot" style="background:{{$item['color']}}"></span>
                                                {{ $item['label'] }}
                                            </span>
                                            <span style="font-size:13px;font-weight:700;color:{{$item['color']}}">{{ number_format($item['pct'],1) }}%</span>
                                        </div>
                                        <div style="height:5px;background:#f1f5f9;border-radius:5px;overflow:hidden;">
                                            <div style="height:100%;width:{{$item['pct']}}%;background:{{$item['color']}};border-radius:5px;transition:width .6s;"></div>
                                        </div>
                                        <div style="font-size:11px;color:#94a3b8;margin-top:3px;">{{ number_format($item['km'],2) }} km</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-chart-pie"></i>
                                <p>Tidak ada data distribusi</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== ROW 2.5: KONDISI PER TAHUN ===== --}}
        <div class="row mb-2">
            <div class="col-12 mb-4">
                <div class="db-card" style="height: 480px;">
                    <div class="db-card-header">
                        <h4><i class="fas fa-chart-bar"></i> Statistik Kondisi Jalan per Tahun</h4>
                        <span class="badge-db blue">
                            <i class="fas fa-calendar"></i> {{ ($conditionByYear ?? collect())->count() }} Tahun
                        </span>
                    </div>
                    <div class="db-card-body" style="height: calc(100% - 57px); padding: 16px 20px;">
                        @if(($conditionByYear ?? collect())->isEmpty())
                            <div class="empty-state">
                                <i class="fas fa-chart-bar"></i>
                                <p>Tidak ada data kondisi jalan per tahun</p>
                            </div>
                        @else
                            <canvas id="conditionByYearChart" style="width:100%;height:100%;"></canvas>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== ROW 3: TOP 5 + KECAMATAN ===== --}}
        <div class="row mb-2">

            {{-- Top 5 Ruas Terburuk --}}
            <div class="col-lg-6 col-12 mb-4">
                <div class="db-card" style="height: 520px;">
                    <div class="db-card-header">
                        <h4><i class="fas fa-exclamation-triangle"></i> Top 5 Ruas Terburuk</h4>
                        <span class="badge-db red"><i class="fas fa-flag"></i> Prioritas Tinggi</span>
                    </div>
                    <div class="db-card-body no-pad overflow-y" style="height: calc(100% - 57px - 53px);">
                        <table class="db-table">
                            <thead>
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Nama Ruas</th>
                                    <th style="text-align:center">SDI</th>
                                    <th style="text-align:center">Kondisi</th>
                                    <th style="text-align:right">Panjang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($worstLinks ?? [] as $index => $link)
                                <tr>
                                    <td><span class="rank-num">{{ $index + 1 }}</span></td>
                                    <td>
                                        <div class="road-name">{{ $link['link_name'] }}</div>
                                        <div class="road-code">{{ $link['link_code'] }}</div>
                                        @if(isset($link['province']))
                                        <div class="road-code"><i class="fas fa-map-marker-alt"></i> {{ $link['province'] }}</div>
                                        @endif
                                    </td>
                                    <td style="text-align:center">
                                        <x-sdi-badge category="Rusak Berat" :sdi-value="$link['avg_sdi']" />
                                    </td>
                                    <td style="text-align:center">
                                        <x-sdi-badge :category="$link['category']" />
                                    </td>
                                    <td style="text-align:right;white-space:nowrap;font-weight:600;font-size:12px;color:#334155;">
                                        {{ number_format($link['total_length'], 2) }} km
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox"></i>
                                            <p>Tidak ada data</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(count($worstLinks ?? []) > 0)
                    <div class="db-card-footer" style="text-align:center;">
                        <a href="{{ route('kondisi-jalan.index') }}" class="btn btn-primary btn-sm">
                            Lihat Semua Ruas <i class="fas fa-chevron-right ml-1"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Top 10 Kecamatan --}}
            <div class="col-lg-6 col-12 mb-4">
                <div class="db-card" style="height: 520px;">
                    <div class="db-card-header">
                        <h4><i class="fas fa-map-marked-alt"></i> Top 10 Kecamatan (SDI Tertinggi)</h4>
                        <span class="badge-db blue"><i class="fas fa-map-pin"></i> Kab. Jember</span>
                    </div>
                    <div class="db-card-body no-pad overflow-y" style="height: calc(100% - 57px);">
                        @forelse($kecamatanStats ?? [] as $index => $kec)
                        <div class="kec-item">
                            <div class="kec-top">
                                <div>
                                    <div class="kec-name">{{ $index + 1 }}. {{ $kec['kecamatan_name'] }}</div>
                                    <div class="kec-meta">{{ $kec['total_links'] }} ruas &bull; {{ number_format($kec['total_length'], 2) }} km</div>
                                </div>
                                <x-sdi-badge :category="$kec['category']" :sdi-value="$kec['avg_sdi']" />
                            </div>
                            @php
                                $tot = max(($kec['good']+$kec['fair']+$kec['light']+$kec['heavy']), 1);
                            @endphp
                            <div class="kec-progress">
                                <div style="width:{{($kec['good']/$tot)*100}}%;background:#2ecc71;"></div>
                                <div style="width:{{($kec['fair']/$tot)*100}}%;background:#FFD700;"></div>
                                <div style="width:{{($kec['light']/$tot)*100}}%;background:#FF8C00;"></div>
                                <div style="width:{{($kec['heavy']/$tot)*100}}%;background:#e74c3c;"></div>
                            </div>
                            <div class="kec-dots">
                                <span class="kec-dot"><span class="dot" style="background:#2ecc71"></span> <span style="color:#64748b">{{ $kec['good'] }}</span></span>
                                <span class="kec-dot"><span class="dot" style="background:#FFD700"></span> <span style="color:#64748b">{{ $kec['fair'] }}</span></span>
                                <span class="kec-dot"><span class="dot" style="background:#FF8C00"></span> <span style="color:#64748b">{{ $kec['light'] }}</span></span>
                                <span class="kec-dot"><span class="dot" style="background:#e74c3c"></span> <span style="color:#64748b">{{ $kec['heavy'] }}</span></span>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-map-marked-alt"></i>
                            <p>Tidak ada data kecamatan</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== ROW 4: DETAIL KERUSAKAN + UPDATE TERBARU ===== --}}
        <div class="row mb-2">

            {{-- Statistik Kerusakan Detail --}}
            <div class="col-lg-8 col-12 mb-4">
                <div class="db-card" style="height: 420px;">
                    <div class="db-card-header">
                        <h4><i class="fas fa-list-alt"></i> Statistik Kerusakan Detail</h4>
                        <a href="{{ route('kondisi-jalan.index') }}" class="badge-db blue" style="text-decoration:none;">
                            Lihat Detail <i class="fas fa-chevron-right ml-1"></i>
                        </a>
                    </div>
                    <div class="db-card-body no-pad" style="height: calc(100% - 57px); overflow-y:auto;">
                        <table class="db-table">
                            <thead>
                                <tr>
                                    <th>Jenis Kerusakan</th>
                                    <th style="text-align:right">Total</th>
                                    <th style="text-align:center">Satuan</th>
                                    <th style="text-align:center">Prioritas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $damageRows = [
                                        ['icon'=>'fas fa-square','color'=>'#ef4444','label'=>'Luas Retak','val'=>number_format($totalCrackArea??0,2),'unit'=>'m²','badge'=>'red','badge_lbl'=>'Kritis'],
                                        ['icon'=>'fas fa-circle','color'=>'#f59e0b','label'=>'Lubang (Pothole)','val'=>number_format($totalPotholes??0),'unit'=>'buah','badge'=>'yellow','badge_lbl'=>'Tinggi'],
                                        ['icon'=>'fas fa-square','color'=>'#ef4444','label'=>'Luas Lubang','val'=>number_format($totalPotholeArea??0,2),'unit'=>'m²','badge'=>'red','badge_lbl'=>'Kritis'],
                                        ['icon'=>'fas fa-square','color'=>'#3b82f6','label'=>'Luas Alur Roda','val'=>number_format($totalRuttingArea??0,2),'unit'=>'m²','badge'=>'blue','badge_lbl'=>'Sedang'],
                                        ['icon'=>'fas fa-square','color'=>'#64748b','label'=>'Luas Patching','val'=>number_format($totalPatchingArea??0,2),'unit'=>'m²','badge'=>'gray','badge_lbl'=>'Monitor'],
                                    ];
                                @endphp
                                @foreach($damageRows as $row)
                                <tr>
                                    <td>
                                        <i class="{{ $row['icon'] }}" style="color:{{ $row['color'] }};margin-right:8px;"></i>
                                        <strong>{{ $row['label'] }}</strong>
                                    </td>
                                    <td style="text-align:right;font-weight:700;color:#0f172a;">{{ $row['val'] }}</td>
                                    <td style="text-align:center;color:#94a3b8;font-size:12px;">{{ $row['unit'] }}</td>
                                    <td style="text-align:center;">
                                        <span class="badge-db {{ $row['badge'] }}">{{ $row['badge_lbl'] }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Update Terbaru --}}
            <div class="col-lg-4 col-12 mb-4">
                <div class="db-card" style="height: 420px;">
                    <div class="db-card-header">
                        <h4><i class="fas fa-clock"></i> Update Terbaru</h4>
                        <span class="badge-db green">
                            <i class="fas fa-check-circle"></i> {{ count($recentUpdates ?? []) }} data
                        </span>
                    </div>
                    <div class="db-card-body no-pad overflow-y" style="height: calc(100% - 57px);">
                        @forelse($recentUpdates ?? [] as $update)
                        <div class="update-item">
                            <div class="update-name">{{ $update['link_name'] }}</div>
                            <div>
                                <x-sdi-badge :category="$update['category']" :sdi-value="$update['sdi_value']" />
                            </div>
                            <div class="update-meta">
                                <div><i class="fas fa-road"></i> Km {{ $update['chainage_from'] }} – {{ $update['chainage_to'] }}</div>
                                <div><i class="fas fa-calendar"></i> Tahun {{ $update['year'] }} &bull; {{ $update['updated_at'] }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>Tidak ada data terbaru</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>
@endsection

@section('page-script')@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
window.addEventListener('load', function () {
    // Destroy stale instances
    if (typeof Chart !== 'undefined') {
        Object.keys(Chart.instances || {}).forEach(k => Chart.instances[k]?.destroy());
    }

    Chart.defaults.font.family = "'Nunito','Segoe UI',sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#64748b';

    const C = {
        baik:        { bg: 'rgba(46,204,113,.85)',  border: 'rgba(46,204,113,1)'  },
        sedang:      { bg: 'rgba(255,215,  0,.85)', border: 'rgba(255,215,  0,1)' },
        rusakRingan: { bg: 'rgba(255,140,  0,.85)', border: 'rgba(255,140,  0,1)' },
        rusakBerat:  { bg: 'rgba(231, 76, 60,.85)', border: 'rgba(231, 76, 60,1)' },
        blue:        { bg: 'rgba(59,130,246,.8)',  border: 'rgba(59,130,246,1)'  },
    };

    function make(id, cfg) {
        const el = document.getElementById(id);
        if (!el) return;
        const ex = Chart.getChart(el);
        if (ex) ex.destroy();
        try { return new Chart(el.getContext('2d'), cfg); } catch(e) { console.error(id, e); }
    }

    // Data dari backend
    const sdiByYear        = {!! json_encode($sdiByYear ?? collect()) !!};
    const conditionByYear  = {!! json_encode($conditionByYear ?? collect()) !!};

    const years      = sdiByYear.map(d => d.year);
    const avgSdi     = sdiByYear.map(d => d.avg_sdi);
    const minSdi     = sdiByYear.map(d => d.min_sdi);
    const maxSdi     = sdiByYear.map(d => d.max_sdi);
    const counts     = sdiByYear.map(d => d.count);

    const cYears     = conditionByYear.map(d => d.year);
    const cBaik      = conditionByYear.map(d => d.baik);
    const cSedang    = conditionByYear.map(d => d.sedang);
    const cRingan    = conditionByYear.map(d => d.rusak_ringan);
    const cBerat     = conditionByYear.map(d => d.rusak_berat);

    const miniOpts = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { enabled: false } },
        scales: { x: { display: false }, y: { display: false } },
        elements: { point: { radius: 0 } }
    };

    // 1. Mini SDI chart
    if (years.length) {
        make('sdi-chart', {
            type: 'line',
            data: { labels: years, datasets: [{ data: avgSdi, borderWidth: 2,
                borderColor: 'rgba(59,130,246,1)', backgroundColor: 'rgba(59,130,246,.1)',
                fill: true, tension: .4 }] },
            options: miniOpts
        });

        // 2. Mini length chart
        make('length-chart', {
            type: 'line',
            data: { labels: years, datasets: [{ data: counts, borderWidth: 2,
                borderColor: 'rgba(139,92,246,1)', backgroundColor: 'rgba(139,92,246,.1)',
                fill: true, tension: .4 }] },
            options: miniOpts
        });

        // 3. SDI Trend
        make('sdiTrendChart', {
            type: 'bar',
            data: { labels: years, datasets: [
                { label: 'Rata-rata SDI', data: avgSdi, backgroundColor: C.blue.bg, borderColor: C.blue.border, borderWidth: 1 },
                { label: 'SDI Min', data: minSdi, type: 'line', borderColor: C.baik.border, backgroundColor: 'transparent', borderWidth: 2, tension: .4, pointRadius: 3 },
                { label: 'SDI Max', data: maxSdi, type: 'line', borderColor: C.rusakBerat.border, backgroundColor: 'transparent', borderWidth: 2, tension: .4, pointRadius: 3 }
            ]},
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: true, position: 'bottom', labels: { usePointStyle: true, padding: 15 } } },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Nilai SDI' }, grid: { color: 'rgba(0,0,0,.04)' } },
                    x: { title: { display: true, text: 'Tahun' }, grid: { display: false } }
                }
            }
        });
    }

    // 4. Donut distribusi
    const total = {{ $totalSegments ?? 0 }};
    if (total > 0) {
        make('conditionDistChart', {
            type: 'doughnut',
            data: {
                labels: ['Baik','Sedang','Rusak Ringan','Rusak Berat'],
                datasets: [{ data: [{{ $goodCondition??0 }},{{ $fairCondition??0 }},{{ $lightDamage??0 }},{{ $heavyDamage??0 }}],
                    backgroundColor: [C.baik.bg, C.sedang.bg, C.rusakRingan.bg, C.rusakBerat.bg],
                    borderWidth: 3, borderColor: '#fff', hoverOffset: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: true, cutout: '65%',
                plugins: { legend: { display: false } }
            }
        });
    }

    // 5. Kondisi per Tahun
    if (cYears.length) {
        make('conditionByYearChart', {
            type: 'bar',
            data: { labels: cYears, datasets: [
                { label: 'Baik',        data: cBaik,   backgroundColor: C.baik.bg,        borderColor: C.baik.border,        borderWidth: 1 },
                { label: 'Sedang',      data: cSedang, backgroundColor: C.sedang.bg,      borderColor: C.sedang.border,      borderWidth: 1 },
                { label: 'Rusak Ringan',data: cRingan, backgroundColor: C.rusakRingan.bg, borderColor: C.rusakRingan.border, borderWidth: 1 },
                { label: 'Rusak Berat', data: cBerat,  backgroundColor: C.rusakBerat.bg,  borderColor: C.rusakBerat.border,  borderWidth: 1 }
            ]},
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: true, position: 'top', labels: { usePointStyle: true, padding: 16, font: { size: 12 } } },
                    tooltip: {
                        callbacks: {
                            footer: items => 'Total: ' + items.reduce((s,i) => s + i.parsed.y, 0) + ' segmen'
                        }
                    }
                },
                scales: {
                    x: { stacked: true, title: { display: true, text: 'Tahun' }, grid: { display: false } },
                    y: { stacked: true, beginAtZero: true, title: { display: true, text: 'Jumlah Segmen' },
                         ticks: { precision: 0 }, grid: { color: 'rgba(0,0,0,.04)' } }
                }
            }
        });
    }
});
</script>
@endpush