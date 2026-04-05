@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Peta Kondisi Jalan Kabupaten Jember</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item">Peta Jember</div>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Peta Kondisi Ruas Jalan Berdasarkan SDI</h4>
                    <div class="card-header-action">
                        <span class="badge badge-info mr-2" id="current-year">
                            Tahun: {{ session('selected_year', date('Y')) }}
                        </span>
                        <span class="badge badge-secondary" id="total-links">Pilih Kecamatan</span>
                    </div>
                </div>
                <div class="card-body p-0" style="position: relative;">
                    
                    <!-- ✅ TOGGLE BUTTON KECAMATAN (Kiri Atas) -->
                    <button id="toggleKecamatanBtn" class="btn btn-primary btn-sm" 
                            style="position: absolute; top: 10px; left: 10px; z-index: 1001; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                        <i class="fas fa-map-marker-alt"></i> Filter Kecamatan
                    </button>

                    <!-- ✅ FILTER KECAMATAN (Collapsible) -->
                    <div id="kecamatanFilter" style="position: absolute; top: 50px; left: 10px; z-index: 1000; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.15); max-height: 70vh; overflow-y: auto; max-width: 250px; display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 style="margin: 0; font-weight: bold; font-size: 13px;">
                                <i class="fas fa-map-marker-alt"></i> Filter Kecamatan
                            </h6>
                            <button class="btn btn-sm btn-link text-danger p-0" id="closeKecamatanFilter" style="font-size: 18px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <!-- Tombol Select All / Deselect All -->
                        <div class="mb-2">
                            <button class="btn btn-sm btn-outline-primary btn-block" id="selectAllKecamatan" style="font-size: 11px;">
                                <i class="fas fa-check-double"></i> Pilih Semua
                            </button>
                        </div>
                        
                        <!-- Loading state -->
                        <div id="kecamatanLoading" class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            <small class="d-block mt-2">Memuat kecamatan...</small>
                        </div>
                        
                        <!-- Checkbox list kecamatan (akan diisi via JS) -->
                        <div id="kecamatanList" style="display: none;"></div>
                        
                        <hr style="margin: 10px 0;">
                        
                        <button class="btn btn-sm btn-success btn-block" id="loadMapData" style="font-size: 11px;">
                            <i class="fas fa-map"></i> Tampilkan Peta
                        </button>
                        <small class="text-muted d-block mt-2" style="font-size: 10px;">
                            <i class="fas fa-info-circle"></i> Pilih minimal 1 kecamatan
                        </small>
                    </div>

                    <!-- ✅ TOGGLE BUTTON KATEGORI SDI (Kanan Atas) -->
                    <button id="toggleCategoryBtn" class="btn btn-primary btn-sm" 
                            style="position: absolute; top: 10px; right: 10px; z-index: 1001; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                        <i class="fas fa-filter"></i> Filter Kategori
                    </button>

                    <!-- Filter Kategori SDI (Collapsible) -->
                    <div id="categoryFilter" style="position: absolute; top: 50px; right: 10px; z-index: 1000; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.15); display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 style="margin: 0; font-weight: bold; font-size: 13px;">
                                <i class="fas fa-filter"></i> Filter Kategori SDI
                            </h6>
                            <button class="btn btn-sm btn-link text-danger p-0" id="closeCategoryFilter" style="font-size: 18px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input category-checkbox" type="checkbox" value="Baik" id="checkBaik" checked>
                            <label class="form-check-label" for="checkBaik" style="font-size: 12px;">
                                <span style="display: inline-block; width: 20px; height: 3px; background: #2ecc71; margin-right: 5px; vertical-align: middle;"></span>
                                Baik
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input category-checkbox" type="checkbox" value="Sedang" id="checkSedang" checked>
                            <label class="form-check-label" for="checkSedang" style="font-size: 12px;">
                                <span style="display: inline-block; width: 20px; height: 3px; background: #f1c40f; margin-right: 5px; vertical-align: middle;"></span>
                                Sedang
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input category-checkbox" type="checkbox" value="Rusak Ringan" id="checkRusakRingan" checked>
                            <label class="form-check-label" for="checkRusakRingan" style="font-size: 12px;">
                                <span style="display: inline-block; width: 20px; height: 3px; background: #e67e22; margin-right: 5px; vertical-align: middle;"></span>
                                Rusak Ringan
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input category-checkbox" type="checkbox" value="Rusak Berat" id="checkRusakBerat" checked>
                            <label class="form-check-label" for="checkRusakBerat" style="font-size: 12px;">
                                <span style="display: inline-block; width: 20px; height: 3px; background: #e74c3c; margin-right: 5px; vertical-align: middle;"></span>
                                Rusak Berat
                            </label>
                        </div>
                        <hr style="margin: 15px 0;">

                        <!-- ✅ TOGGLE DATA DRAINASE 3 KECAMATAN -->
                        <h6 style="margin: 0 0 10px 0; font-weight: bold; font-size: 13px;">
                            <i class="fas fa-water"></i> Data Genangan Air
                        </h6>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="toggleKaliwates">
                            <label class="form-check-label" for="toggleKaliwates" style="font-size: 12px;">
                                <span style="display: inline-block; width: 22px; height: 22px; background: #2196F3; margin-right: 5px; vertical-align: middle; border-radius: 50%; text-align: center; line-height: 22px; color: white; font-size: 11px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">K</span>
                                Kaliwates
                            </label>
                        </div>
                        <small class="text-muted d-block" style="font-size: 10px; margin-top: -5px; margin-bottom: 8px; margin-left: 27px;">
                            18 lokasi (2 titik, 15 garis, 1 area)
                        </small>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="togglePatrang">
                            <label class="form-check-label" for="togglePatrang" style="font-size: 12px;">
                                <span style="display: inline-block; width: 22px; height: 22px; background: #1976D2; margin-right: 5px; vertical-align: middle; border-radius: 50%; text-align: center; line-height: 22px; color: white; font-size: 11px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">P</span>
                                Patrang
                            </label>
                        </div>
                        <small class="text-muted d-block" style="font-size: 10px; margin-top: -5px; margin-bottom: 8px; margin-left: 27px;">
                            12 lokasi (4 titik, 8 garis)
                        </small>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="toggleSumbersari">
                            <label class="form-check-label" for="toggleSumbersari" style="font-size: 12px;">
                                <span style="display: inline-block; width: 22px; height: 22px; background: #388E3C; margin-right: 5px; vertical-align: middle; border-radius: 50%; text-align: center; line-height: 22px; color: white; font-size: 11px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">S</span>
                                Sumbersari
                            </label>
                        </div>
                        <small class="text-muted d-block" style="font-size: 10px; margin-top: -5px; margin-left: 27px;">
                            25 lokasi (8 titik, 17 garis)
                        </small>

                        <div class="mt-2 p-2" style="background: #e3f2fd; border-radius: 4px; border-left: 3px solid #2196F3;">
                            <small style="font-size: 10px; color: #1565C0;">
                                <i class="fas fa-info-circle"></i> <strong>Total: 55 lokasi</strong> data genangan air
                            </small>
                        </div>
                        <hr style="margin: 10px 0;">
                        <button class="btn btn-sm btn-primary btn-block" id="applyFilter" style="font-size: 11px;">
                            <i class="fas fa-check"></i> Terapkan
                        </button>
                        <button class="btn btn-sm btn-outline-secondary btn-block" id="resetFilter" style="font-size: 11px;">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>

                    <div id="map" style="height: 80vh; width: 100%;"></div>
                </div>
            </div>

            <!-- ✅ CARD INFORMASI DATA -->
            <div class="row mt-4" id="infoDataCard" style="display: none;">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-info-circle"></i> Informasi Data Peta</h4>
                            <div class="card-header-action">
                                <span class="badge" id="info-success-badge">-</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Total Segmen -->
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                                    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                                        <div class="card-body">
                                            <h6 class="mb-3" style="opacity: 0.9;">
                                                <i class="fas fa-chart-pie"></i> Total Segmen
                                            </h6>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span style="font-size: 13px;">Ditemukan:</span>
                                                <strong style="font-size: 20px;" id="info-total-segments">-</strong>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span style="font-size: 13px;">Ditampilkan:</span>
                                                <strong style="font-size: 20px; color: #4ade80;" id="info-displayed-segments">-</strong>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span style="font-size: 13px;">Tidak Ditampilkan:</span>
                                                <strong style="font-size: 20px; color: #f87171;" id="info-skipped-segments">-</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Koordinat -->
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                                    <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none;">
                                        <div class="card-body">
                                            <h6 class="mb-3" style="opacity: 0.9;">
                                                <i class="fas fa-map-marker-alt"></i> Status Koordinat
                                            </h6>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span style="font-size: 13px;">Range Koordinat:</span>
                                                <strong style="font-size: 20px; color: #4ade80;" id="info-range-coords">-</strong>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span style="font-size: 13px;">Fallback Koordinat:</span>
                                                <strong style="font-size: 20px; color: #fbbf24;" id="info-fallback-coords">-</strong>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span style="font-size: 13px;">Tanpa Koordinat:</span>
                                                <strong style="font-size: 20px; color: #F80000;" id="info-no-coords">-</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tingkat Keberhasilan -->
                                <div class="col-lg-4 col-md-12 col-sm-12 mb-3">
                                    <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; border: none;">
                                        <div class="card-body">
                                            <h6 class="mb-3" style="opacity: 0.9;">
                                                <i class="fas fa-check-circle"></i> Tingkat Keberhasilan
                                            </h6>
                                            <div class="text-center mb-3">
                                                <div style="font-size: 48px; font-weight: bold;" id="info-success-rate-big">-</div>
                                                <div style="font-size: 14px; opacity: 0.9;">Success Rate</div>
                                            </div>
                                            <div class="progress" style="height: 10px; background: rgba(255,255,255,0.3);">
                                                <div class="progress-bar" role="progressbar" id="info-success-bar" 
                                                     style="width: 0%; background: white;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Warning Alert -->
                            <div id="info-warnings" style="display: none;">
                                <div class="alert alert-warning mb-0" style="border-left: 4px solid #ffc107;">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-exclamation-triangle fa-2x mr-3" style="color: #856404;"></i>
                                        <div style="flex: 1;">
                                            <h6 class="alert-heading mb-2" style="color: #856404;">
                                                <strong>Peringatan Kualitas Data</strong>
                                            </h6>
                                            <p id="info-warning-text" class="mb-0" style="color: #856404; font-size: 14px;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Info Box -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-info mb-0" style="border-left: 4px solid #17a2b8;">
                                        <h6 class="alert-heading" style="color: #0c5460;">
                                            <i class="fas fa-lightbulb"></i> Penjelasan Istilah
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong style="color: #0c5460;">Range Koordinat:</strong>
                                                <p class="mb-0" style="font-size: 13px; color: #0c5460;">
                                                    Segmen yang menggunakan koordinat tepat dalam rentang chainage yang ditentukan.
                                                </p>
                                            </div>
                                            <div class="col-md-4">
                                                <strong style="color: #0c5460;">Fallback Koordinat:</strong>
                                                <p class="mb-0" style="font-size: 13px; color: #0c5460;">
                                                    Segmen yang menggunakan koordinat terdekat karena koordinat dalam range tidak tersedia.
                                                </p>
                                            </div>
                                            <div class="col-md-4">
                                                <strong style="color: #0c5460;">Tidak Ditampilkan:</strong>
                                                <p class="mb-0" style="font-size: 13px; color: #0c5460;">
                                                    Segmen yang tidak memiliki koordinat valid sama sekali dan tidak dapat ditampilkan di peta.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Statistik Panjang per Kategori -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-chart-bar"></i> Statistik Panjang Jalan per Kategori</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div class="card card-statistic-1" style="border-left: 4px solid #2ecc71;">
                                        <div class="card-icon" style="background-color: #2ecc71;">
                                            <i class="fas fa-road"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Baik</h4>
                                            </div>
                                            <div class="card-body d-flex align-items-baseline" id="stat-baik-wrap">
                                                <span id="stat-baik"><span class="text-muted">-</span></span>
                                                <span id="pct-baik" class="ml-2" style="font-size: 14px; font-weight: 500; color: #2ecc71;"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div class="card card-statistic-1" style="border-left: 4px solid #f1c40f;">
                                        <div class="card-icon" style="background-color: #f1c40f;">
                                            <i class="fas fa-road"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Sedang</h4>
                                            </div>
                                            <div class="card-body d-flex align-items-baseline" id="stat-sedang-wrap">
                                                <span id="stat-sedang"><span class="text-muted">-</span></span>
                                                <span id="pct-sedang" class="ml-2" style="font-size: 14px; font-weight: 500; color: #d4ac0d;"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div class="card card-statistic-1" style="border-left: 4px solid #e67e22;">
                                        <div class="card-icon" style="background-color: #e67e22;">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Rusak Ringan</h4>
                                            </div>
                                            <div class="card-body d-flex align-items-baseline" id="stat-rusak-ringan-wrap">
                                                <span id="stat-rusak-ringan"><span class="text-muted">-</span></span>
                                                <span id="pct-rusak-ringan" class="ml-2" style="font-size: 14px; font-weight: 500; color: #e67e22;"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div class="card card-statistic-1" style="border-left: 4px solid #e74c3c;">
                                        <div class="card-icon" style="background-color: #e74c3c;">
                                            <i class="fas fa-times-circle"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Rusak Berat</h4>
                                            </div>
                                            <div class="card-body d-flex align-items-baseline" id="stat-rusak-berat-wrap">
                                                <span id="stat-rusak-berat"><span class="text-muted">-</span></span>
                                                <span id="pct-rusak-berat" class="ml-2" style="font-size: 14px; font-weight: 500; color: #e74c3c;"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ✅ CARD REKOMENDASI PENANGANAN JALAN (PKRMS) — Menggantikan Estimasi Biaya -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-tools"></i> Rekomendasi Penanganan Jalan</h4>
                            <div class="card-header-action">
                                <span class="badge badge-primary">Berdasarkan Standar PKRMS</span>
                            </div>
                        </div>
                        <div class="card-body">

                            <!-- Info Box Sumber PKRMS -->
                            <div class="alert alert-info mb-4" style="border-left: 4px solid #17a2b8;">
                                <h6 class="alert-heading" style="color: #0c5460;">
                                    <i class="fas fa-book"></i> Dasar Penentuan Rekomendasi (PKRMS)
                                </h6>
                                <div class="row" style="font-size: 13px; color: #0c5460;">
                                    <div class="col-md-3">
                                        <strong>Baik:</strong> PR, RK
                                        <small class="d-block">Pemeliharaan Rutin + Pemeliharaan Rutin Kondisi</small>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Sedang:</strong> PR, RK, PB
                                        <small class="d-block">+ Pemeliharaan Berkala</small>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Rusak Ringan:</strong> PR, RK, RH
                                        <small class="d-block">+ Rehabilitasi</small>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Rusak Berat:</strong> PR, RK, RH, PL
                                        <small class="d-block">+ Peningkatan/Rekonstruksi</small>
                                    </div>
                                </div>
                                <small class="d-block mt-2" style="color: #555;">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Keterangan:</strong> PR = Pemeliharaan Rutin &nbsp;|&nbsp; RK = Pemeliharaan Rutin Kondisi &nbsp;|&nbsp; PB = Pemeliharaan Berkala &nbsp;|&nbsp; RH = Rehabilitasi &nbsp;|&nbsp; PL = Peningkatan/Rekonstruksi
                                </small>
                            </div>

                            <!-- Kartu Rekomendasi 4 Kondisi -->
                            <div class="row">

                                <!-- BAIK -->
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div class="card h-100" style="border-left: 4px solid #2ecc71; border-radius: 8px;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div style="width: 48px; height: 48px; background: #2ecc71; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                                                    <i class="fas fa-check-circle" style="font-size: 22px; color: white;"></i>
                                                </div>
                                                <div>
                                                    <h5 style="margin: 0; font-weight: 700; color: #2ecc71;">Baik</h5>
                                                    <small class="text-muted">SDI ≤ 2</small>
                                                </div>
                                                <div class="ml-auto text-right">
                                                    <div style="font-size: 20px; font-weight: 700; color: #333;" id="rek-panjang-baik">-</div>
                                                    <small id="rek-pct-baik" style="color: #2ecc71; font-weight: 600;"></small>
                                                </div>
                                            </div>
                                            <hr style="margin: 10px 0;">
                                            <p style="font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                                <i class="fas fa-wrench" style="color: #2ecc71;"></i> Jenis Penanganan:
                                            </p>
                                            <div class="mb-1">
                                                <span class="badge" style="background: #d4edda; color: #155724; font-size: 12px; padding: 5px 10px; margin-right: 4px; margin-bottom: 4px; border-radius: 20px;">
                                                    PR — Pemeliharaan Rutin
                                                </span>
                                                <span class="badge" style="background: #d4edda; color: #155724; font-size: 12px; padding: 5px 10px; margin-right: 4px; margin-bottom: 4px; border-radius: 20px;">
                                                    RK — Pemeliharaan Rutin Kondisi
                                                </span>
                                            </div>
                                            <p class="text-muted mb-0" style="font-size: 12px; margin-top: 6px;">
                                                <i class="fas fa-info-circle"></i> Jalan dalam kondisi baik, cukup dengan pemeliharaan rutin untuk mempertahankan kondisi.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- SEDANG -->
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div class="card h-100" style="border-left: 4px solid #f1c40f; border-radius: 8px;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div style="width: 48px; height: 48px; background: #f1c40f; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                                                    <i class="fas fa-road" style="font-size: 22px; color: white;"></i>
                                                </div>
                                                <div>
                                                    <h5 style="margin: 0; font-weight: 700; color: #d4ac0d;">Sedang</h5>
                                                    <small class="text-muted">SDI 2 – 4</small>
                                                </div>
                                                <div class="ml-auto text-right">
                                                    <div style="font-size: 20px; font-weight: 700; color: #333;" id="rek-panjang-sedang">-</div>
                                                    <small id="rek-pct-sedang" style="color: #d4ac0d; font-weight: 600;"></small>
                                                </div>
                                            </div>
                                            <hr style="margin: 10px 0;">
                                            <p style="font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                                <i class="fas fa-wrench" style="color: #f1c40f;"></i> Jenis Penanganan:
                                            </p>
                                            <div class="mb-1">
                                                <span class="badge" style="background: #fff3cd; color: #856404; font-size: 12px; padding: 5px 10px; margin-right: 4px; margin-bottom: 4px; border-radius: 20px;">
                                                    PR — Pemeliharaan Rutin
                                                </span>
                                                <span class="badge" style="background: #fff3cd; color: #856404; font-size: 12px; padding: 5px 10px; margin-right: 4px; margin-bottom: 4px; border-radius: 20px;">
                                                    RK — Pemeliharaan Rutin Kondisi
                                                </span>
                                                <span class="badge" style="background: #fff3cd; color: #856404; font-size: 12px; padding: 5px 10px; margin-right: 4px; margin-bottom: 4px; border-radius: 20px;">
                                                    PB — Pemeliharaan Berkala
                                                </span>
                                            </div>
                                            <p class="text-muted mb-0" style="font-size: 12px; margin-top: 6px;">
                                                <i class="fas fa-info-circle"></i> Diperlukan pemeliharaan berkala untuk mencegah penurunan kondisi lebih lanjut.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <!-- RUSAK RINGAN -->
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div class="card h-100" style="border-left: 4px solid #e67e22; border-radius: 8px;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div style="width: 48px; height: 48px; background: #e67e22; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                                                    <i class="fas fa-exclamation-triangle" style="font-size: 22px; color: white;"></i>
                                                </div>
                                                <div>
                                                    <h5 style="margin: 0; font-weight: 700; color: #e67e22;">Rusak Ringan</h5>
                                                    <small class="text-muted">SDI 4 – 6</small>
                                                </div>
                                                <div class="ml-auto text-right">
                                                    <div style="font-size: 20px; font-weight: 700; color: #333;" id="rek-panjang-rusak-ringan">-</div>
                                                    <small id="rek-pct-rusak-ringan" style="color: #e67e22; font-weight: 600;"></small>
                                                </div>
                                            </div>
                                            <hr style="margin: 10px 0;">
                                            <p style="font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                                <i class="fas fa-wrench" style="color: #e67e22;"></i> Jenis Penanganan:
                                            </p>
                                            <div class="mb-1">
                                                <span class="badge" style="background: #fde8d4; color: #7d3c12; font-size: 12px; padding: 5px 10px; margin-right: 4px; margin-bottom: 4px; border-radius: 20px;">
                                                    PR — Pemeliharaan Rutin
                                                </span>
                                                <span class="badge" style="background: #fde8d4; color: #7d3c12; font-size: 12px; padding: 5px 10px; margin-right: 4px; margin-bottom: 4px; border-radius: 20px;">
                                                    RK — Pemeliharaan Rutin Kondisi
                                                </span>
                                                <span class="badge" style="background: #fde8d4; color: #7d3c12; font-size: 12px; padding: 5px 10px; margin-right: 4px; margin-bottom: 4px; border-radius: 20px;">
                                                    RH — Rehabilitasi
                                                </span>
                                            </div>
                                            <p class="text-muted mb-0" style="font-size: 12px; margin-top: 6px;">
                                                <i class="fas fa-info-circle"></i> Diperlukan rehabilitasi untuk memulihkan kapasitas struktural jalan yang menurun.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- RUSAK BERAT -->
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div class="card h-100" style="border-left: 4px solid #e74c3c; border-radius: 8px;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div style="width: 48px; height: 48px; background: #e74c3c; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                                                    <i class="fas fa-times-circle" style="font-size: 22px; color: white;"></i>
                                                </div>
                                                <div>
                                                    <h5 style="margin: 0; font-weight: 700; color: #e74c3c;">Rusak Berat</h5>
                                                    <small class="text-muted">SDI > 6</small>
                                                </div>
                                                <div class="ml-auto text-right">
                                                    <div style="font-size: 20px; font-weight: 700; color: #333;" id="rek-panjang-rusak-berat">-</div>
                                                    <small id="rek-pct-rusak-berat" style="color: #e74c3c; font-weight: 600;"></small>
                                                </div>
                                            </div>
                                            <hr style="margin: 10px 0;">
                                            <p style="font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                                <i class="fas fa-wrench" style="color: #e74c3c;"></i> Jenis Penanganan:
                                            </p>
                                            <div class="mb-1">
                                                <span class="badge" style="background: #fde8e8; color: #7b1818; font-size: 12px; padding: 5px 10px; margin-right: 4px; margin-bottom: 4px; border-radius: 20px;">
                                                    PR — Pemeliharaan Rutin
                                                </span>
                                                <span class="badge" style="background: #fde8e8; color: #7b1818; font-size: 12px; padding: 5px 10px; margin-right: 4px; margin-bottom: 4px; border-radius: 20px;">
                                                    RK — Pemeliharaan Rutin Kondisi
                                                </span>
                                                <span class="badge" style="background: #fde8e8; color: #7b1818; font-size: 12px; padding: 5px 10px; margin-right: 4px; margin-bottom: 4px; border-radius: 20px;">
                                                    RH — Rehabilitasi
                                                </span>
                                                <span class="badge" style="background: #fde8e8; color: #7b1818; font-size: 12px; padding: 5px 10px; margin-right: 4px; margin-bottom: 4px; border-radius: 20px;">
                                                    PL — Peningkatan/Rekonstruksi
                                                </span>
                                            </div>
                                            <p class="text-muted mb-0" style="font-size: 12px; margin-top: 6px;">
                                                <i class="fas fa-info-circle"></i> Diperlukan peningkatan atau rekonstruksi menyeluruh karena kerusakan struktural yang parah.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Ringkasan Total -->
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 8px;">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-1 text-center">
                                                    <i class="fas fa-route" style="font-size: 36px; color: white; opacity: 0.9;"></i>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5 style="color: white; margin: 0; font-weight: 700;">Total Panjang Jalan Ditampilkan</h5>
                                                    <small style="color: rgba(255,255,255,0.8);">Seluruh kategori kondisi yang aktif</small>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <div style="font-size: 28px; font-weight: 700; color: white;" id="rek-total-panjang">-</div>
                                                    <small style="color: rgba(255,255,255,0.8);">Total Panjang</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <div style="font-size: 13px; color: rgba(255,255,255,0.9);" id="rek-perlu-penanganan">
                                                        <i class="fas fa-tools"></i> Memerlukan penanganan: <strong id="rek-panjang-perlu">-</strong>
                                                        <span id="rek-pct-perlu" style="color: #fbbf24;"></span>
                                                    </div>
                                                    <div style="font-size: 13px; color: rgba(255,255,255,0.9); margin-top: 4px;">
                                                        <i class="fas fa-check"></i> Kondisi baik (PR saja): <strong id="rek-panjang-aman">-</strong>
                                                        <span id="rek-pct-aman" style="color: #4ade80;"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 80vh;
        width: 100%;
        border-radius: 0 0 0.25rem 0.25rem;
    }

    .leaflet-control-attribution {
        display: none !important;
    }

    .leaflet-popup-content {
        margin: 10px;
        min-width: 220px;
    }

    .leaflet-popup-content strong {
        color: #333;
        font-size: 13px;
    }

    .form-check {
        margin-bottom: 8px;
    }

    .form-check-label {
        cursor: pointer;
        user-select: none;
    }

    .form-check-input {
        cursor: pointer;
    }

    #categoryFilter, #kecamatanFilter {
        max-width: 250px;
    }

    .card-statistic-1 {
        padding: 20px;
        position: relative;
        margin-bottom: 20px;
    }

    .card-statistic-1 .card-icon {
        width: 80px;
        height: 80px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        float: left;
        margin-right: 15px;
    }

    .card-statistic-1 .card-icon i {
        font-size: 32px;
        color: white;
    }

    .card-statistic-1 .card-wrap {
        overflow: hidden;
    }

    .card-statistic-1 .card-header h4 {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #6c757d;
        text-transform: uppercase;
    }

    .card-statistic-1 .card-body {
        font-size: 24px;
        font-weight: 700;
        color: #333;
    }

    #kecamatanList .form-check {
        padding-left: 5px;
    }

    #kecamatanList .form-check-label {
        font-size: 11px;
        font-weight: 500;
    }

    #infoDataCard {
        animation: slideUp 0.5s ease-out;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    #kecamatanFilter, #categoryFilter {
        transition: all 0.3s ease-in-out;
    }
</style>
@endpush

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let map = L.map('map').setView([-8.172, 113.687], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    const sdiColors = {
        'Baik': '#2ecc71',
        'Sedang': '#f1c40f',
        'Rusak Ringan': '#e67e22',
        'Rusak Berat': '#e74c3c',
        'Tidak Ada Data': '#95a5a6'
    };

    let allSegments = [];
    let polylines = [];
    let selectedCategories = ['Baik', 'Sedang', 'Rusak Ringan', 'Rusak Berat'];
    let kecamatanData = [];
    let currentStats = null;

    const selectedYear = {{ session('selected_year', date('Y')) }};

    loadKecamatanList();

    // ==================== DATA DRAINASE ====================
    let dataKaliwates = [], dataPatrang = [], dataSumbersari = [];
    let kaliwatesLayer = L.layerGroup();
    let patrangLayer = L.layerGroup();
    let sumbersariLayer = L.layerGroup();
    let showKaliwates = false, showPatrang = false, showSumbersari = false;

    function loadKaliwatesData() {
        fetch('/data/kaliwates.json')
            .then(r => { if (!r.ok) throw new Error('Not found'); return r.json(); })
            .then(geojson => { dataKaliwates = geojson.features; renderKaliwatesLayer(); })
            .catch(e => console.error('❌ Kaliwates:', e));
    }

    function loadPatrangData() {
        fetch('/data/patrang.json')
            .then(r => { if (!r.ok) throw new Error('Not found'); return r.json(); })
            .then(geojson => { dataPatrang = geojson.features; renderPatrangLayer(); })
            .catch(e => console.error('❌ Patrang:', e));
    }

    function loadSumbersariData() {
        fetch('/data/sumbersari.json')
            .then(r => { if (!r.ok) throw new Error('Not found'); return r.json(); })
            .then(geojson => { dataSumbersari = geojson.features; renderSumbersariLayer(); })
            .catch(e => console.error('❌ Sumbersari:', e));
    }

    function renderDrainaseLayer(data, layer, color, letter, kecNama) {
        layer.clearLayers();
        const style = { color, weight: 4, opacity: 0.8, fillColor: color, fillOpacity: 0.3 };
        const icon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background:${color};width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:2px solid white;box-shadow:0 2px 5px rgba(0,0,0,0.3);font-size:12px;color:white;font-weight:bold;">${letter}</div>`,
            iconSize: [24, 24], iconAnchor: [12, 12]
        });
        data.forEach(feature => {
            const props = feature.properties, geom = feature.geometry;
            const popup = `<div style="font-family:Arial,sans-serif;"><strong style="color:${color};">🌊 ${props.name}</strong><hr style="margin:8px 0;"><table style="font-size:12px;"><tr><td><strong>Kecamatan:</strong></td><td>${kecNama}</td></tr><tr><td><strong>Tipe:</strong></td><td>${geom.type}</td></tr></table></div>`;
            if (geom.type === 'Point') {
                layer.addLayer(L.marker([geom.coordinates[1], geom.coordinates[0]], { icon }).bindPopup(popup));
            } else if (geom.type === 'LineString') {
                layer.addLayer(L.polyline(geom.coordinates.map(c => [c[1], c[0]]), style).bindPopup(popup));
            } else if (geom.type === 'Polygon') {
                layer.addLayer(L.polygon(geom.coordinates[0].map(c => [c[1], c[0]]), style).bindPopup(popup));
            }
        });
    }

    function renderKaliwatesLayer() {
        renderDrainaseLayer(dataKaliwates, kaliwatesLayer, '#2196F3', 'K', 'Kaliwates');
        if (showKaliwates) kaliwatesLayer.addTo(map);
    }
    function renderPatrangLayer() {
        renderDrainaseLayer(dataPatrang, patrangLayer, '#1976D2', 'P', 'Patrang');
        if (showPatrang) patrangLayer.addTo(map);
    }
    function renderSumbersariLayer() {
        renderDrainaseLayer(dataSumbersari, sumbersariLayer, '#388E3C', 'S', 'Sumbersari');
        if (showSumbersari) sumbersariLayer.addTo(map);
    }

    function toggleLayer(show, layer) {
        if (show && !map.hasLayer(layer)) layer.addTo(map);
        else if (!show && map.hasLayer(layer)) map.removeLayer(layer);
    }

    // ==================== TOGGLE BUTTONS ====================
    $('#toggleKecamatanBtn').on('click', function() { $('#kecamatanFilter').slideToggle(300); $(this).toggleClass('active'); });
    $('#closeKecamatanFilter').on('click', function() { $('#kecamatanFilter').slideUp(300); $('#toggleKecamatanBtn').removeClass('active'); });
    $('#toggleCategoryBtn').on('click', function() { $('#categoryFilter').slideToggle(300); $(this).toggleClass('active'); });
    $('#closeCategoryFilter').on('click', function() { $('#categoryFilter').slideUp(300); $('#toggleCategoryBtn').removeClass('active'); });

    $('#toggleKaliwates').on('change', function() { showKaliwates = $(this).is(':checked'); toggleLayer(showKaliwates, kaliwatesLayer); });
    $('#togglePatrang').on('change', function() { showPatrang = $(this).is(':checked'); toggleLayer(showPatrang, patrangLayer); });
    $('#toggleSumbersari').on('change', function() { showSumbersari = $(this).is(':checked'); toggleLayer(showSumbersari, sumbersariLayer); });

    // ==================== EVENT LISTENERS ====================
    $('#loadMapData').on('click', function() {
        const selected = getSelectedKecamatan();
        if (selected.length === 0) { alert('⚠️ Pilih minimal 1 kecamatan terlebih dahulu!'); return; }
        loadMapData(selectedYear, selected);
    });

    $('#selectAllKecamatan').on('click', function() {
        const allChecked = $('.kecamatan-checkbox:checked').length === $('.kecamatan-checkbox').length;
        $('.kecamatan-checkbox').prop('checked', !allChecked);
        $(this).html(allChecked ? '<i class="fas fa-check-double"></i> Pilih Semua' : '<i class="fas fa-times"></i> Batalkan Semua');
    });

    $('#applyFilter').on('click', function() { applyFilter(); });
    $('#resetFilter').on('click', function() { $('.category-checkbox').prop('checked', true); applyFilter(); });

    // ==================== FUNCTIONS ====================
    function loadKecamatanList() {
        $('#kecamatanLoading').show();
        $('#kecamatanList').hide();
        $.ajax({
            url: '/api/alignment/kecamatan-list', type: 'GET', data: { year: selectedYear },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    kecamatanData = response.data;
                    renderKecamatanCheckboxes(response.data);
                    $('#kecamatanLoading').hide();
                    $('#kecamatanList').show();
                } else {
                    $('#kecamatanLoading').html('<small class="text-danger">Tidak ada data kecamatan</small>');
                }
            },
            error: function() { $('#kecamatanLoading').html('<small class="text-danger">Gagal memuat data</small>'); }
        });
    }

    function renderKecamatanCheckboxes(data) {
        let html = '';
        data.forEach(kec => {
            html += `<div class="form-check"><input class="form-check-input kecamatan-checkbox" type="checkbox" value="${kec.kecamatan_code}" id="kec_${kec.kecamatan_code}"><label class="form-check-label" for="kec_${kec.kecamatan_code}">${kec.kecamatan_name}<small class="text-muted d-block" style="font-size:9px;">${kec.total_links} ruas | ${kec.total_segments} segmen</small></label></div>`;
        });
        $('#kecamatanList').html(html);
    }

    function getSelectedKecamatan() {
        let selected = [];
        $('.kecamatan-checkbox:checked').each(function() { selected.push($(this).val()); });
        return selected;
    }

    function loadMapData(year, kecamatanCodes) {
        $.ajax({
            url: '/api/alignment/coords-sdi-by-kecamatan', type: 'GET',
            data: { year, kecamatan_codes: kecamatanCodes },
            timeout: 600000,
            beforeSend: function() {
                $('#loadMapData').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Memuat...');
                $('#total-links').html('<span class="spinner-border spinner-border-sm"></span> Loading...');
                polylines.forEach(p => map.removeLayer(p));
                polylines = [];
                $('#infoDataCard').fadeOut(200);
            },
            success: function(response) {
                if (response.stats) { currentStats = response.stats; updateInfoCard(response.stats); }
                if (response.success && response.data && response.data.length > 0) {
                    allSegments = response.data;
                    renderSegments();
                } else {
                    $('#total-links').html('Tidak ada data');
                    alert(response.message || 'Tidak ada data untuk kecamatan yang dipilih');
                }
            },
            error: function(xhr, status, error) {
                if (status === 'timeout') { alert('Permintaan terlalu lama. Coba kurangi jumlah kecamatan.'); }
                else if (xhr.status === 422) { alert('Validasi gagal: ' + (xhr.responseJSON?.details ? Object.values(xhr.responseJSON.details).flat().join(', ') : '')); }
                else if (xhr.status === 400) { alert(xhr.responseJSON?.message || 'Pilih minimal 1 kecamatan'); }
                else { alert('Gagal memuat data peta. Silakan refresh halaman.'); }
                $('#total-links').html('Error');
            },
            complete: function() {
                $('#loadMapData').prop('disabled', false).html('<i class="fas fa-map"></i> Tampilkan Peta');
            }
        });
    }

    function updateInfoCard(stats) {
        $('#infoDataCard').fadeIn(400);
        $('#info-total-segments').text(stats.total);
        $('#info-displayed-segments').text(stats.displayed);
        $('#info-skipped-segments').text(stats.skipped);
        $('#info-range-coords').text(stats.range_coords);
        $('#info-fallback-coords').text(stats.fallback);
        $('#info-no-coords').text(stats.skipped);

        const successRate = parseFloat(stats.success_rate);
        $('#info-success-rate-big').text(stats.success_rate);
        $('#info-success-bar').css('width', successRate + '%');

        let badgeClass = successRate >= 90 ? 'badge-success' : (successRate >= 70 ? 'badge-warning' : 'badge-danger');
        let badgeText  = successRate >= 90 ? 'Sangat Baik' : (successRate >= 70 ? 'Perlu Perhatian' : 'Perlu Perbaikan');
        $('#info-success-badge').removeClass('badge-success badge-warning badge-danger').addClass(badgeClass).text(badgeText + ' (' + stats.success_rate + ')');

        if (stats.skipped > 0) {
            const skipPct = ((stats.skipped / stats.total) * 100).toFixed(1);
            let msg = `${stats.skipped} segmen (${skipPct}%) tidak memiliki koordinat yang valid.`;
            if (skipPct > 20) msg += ` <strong>Persentase ini cukup tinggi</strong>, disarankan periksa kelengkapan data koordinat.`;
            $('#info-warning-text').html(msg);
            $('#info-warnings').fadeIn(300);
        } else {
            $('#info-warnings').hide();
        }

        $('html, body').animate({ scrollTop: $('#infoDataCard').offset().top - 100 }, 800);
    }

    function applyFilter() {
        selectedCategories = [];
        $('.category-checkbox:checked').each(function() { selectedCategories.push($(this).val()); });
        renderSegments();
    }

    function renderSegments() {
        polylines.forEach(p => map.removeLayer(p));
        polylines = [];

        let totalSegments = 0, allBounds = [];
        let lengthStats = { 'Baik': 0, 'Sedang': 0, 'Rusak Ringan': 0, 'Rusak Berat': 0 };
        let countStats  = { 'Baik': 0, 'Sedang': 0, 'Rusak Ringan': 0, 'Rusak Berat': 0 };

        const filteredSegments = allSegments.filter(s => selectedCategories.includes(s.category));

        filteredSegments.forEach(segment => {
            if (!segment.coords || segment.coords.length < 2) return;

            totalSegments++;
            countStats[segment.category]++;
            let segLen = segment.chainage_to - segment.chainage_from;
            lengthStats[segment.category] += segLen;

            let coords = segment.coords.map(c => [c.lat, c.lng]);
            let color  = sdiColors[segment.category] || '#95a5a6';

            let polyline = L.polyline(coords, { color, weight: 6, opacity: 0.85, smoothFactor: 1 }).addTo(map);
            polylines.push(polyline);

            const sdiVal     = segment.sdi_final !== null ? segment.sdi_final.toFixed(2) : 'N/A';
            const fromKm     = (segment.chainage_from / 1000).toFixed(3);
            const toKm       = (segment.chainage_to / 1000).toFixed(3);
            const lenM       = segLen.toFixed(0);

            polyline.bindPopup(`
                <div style="font-family:Arial,sans-serif;">
                    <strong style="font-size:16px;color:#333;">Ruas ${segment.link_no}</strong><br>
                    <div style="font-size:13px;color:#666;margin-top:4px;font-weight:500;">${segment.link_name || 'Nama ruas tidak tersedia'}</div>
                    <hr style="margin:8px 0;border-color:#ddd;">
                    <table style="width:100%;font-size:12px;">
                        <tr><td><strong>Chainage:</strong></td><td>${fromKm} - ${toKm} km</td></tr>
                        <tr><td><strong>Panjang:</strong></td><td><strong>${lenM} M</strong></td></tr>
                        <tr><td><strong>Kategori:</strong></td><td><span style="background:${color};color:white;padding:2px 8px;border-radius:3px;font-weight:bold;font-size:11px;">${segment.category}</span></td></tr>
                        <tr><td><strong>Nilai SDI:</strong></td><td><strong>${sdiVal}</strong></td></tr>
                        <tr><td><strong>Tahun:</strong></td><td>${segment.year} (ref: ${segment.reference_year || 'N/A'})</td></tr>
                    </table>
                </div>
            `);

            polyline.on('mouseover', function() { this.setStyle({ weight: 8, opacity: 1 }); });
            polyline.on('mouseout',  function() { this.setStyle({ weight: 6, opacity: 0.85 }); });

            allBounds.push(...coords);
        });

        let uniqueLinks = [...new Set(filteredSegments.filter(s => s.coords && s.coords.length >= 2).map(s => s.link_no))];
        $('#total-links').html(
            `${uniqueLinks.length} Ruas | ${totalSegments} Segmen | ` +
            `<span style="color:#2ecc71">B:${countStats['Baik']}</span> ` +
            `<span style="color:#FFD700">S:${countStats['Sedang']}</span> ` +
            `<span style="color:#FFA500">RR:${countStats['Rusak Ringan']}</span> ` +
            `<span style="color:#e74c3c">RB:${countStats['Rusak Berat']}</span>`
        );

        updateLengthStatistics(lengthStats);

        if (allBounds.length > 0) map.fitBounds(L.latLngBounds(allBounds), { padding: [50, 50] });
    }

    function updateLengthStatistics(lengthStats) {
        const totalAllLength = Object.values(lengthStats).reduce((s, v) => s + v, 0);

        function calcPct(val) {
            return totalAllLength === 0 ? '0%' : ((val / totalAllLength) * 100).toFixed(1) + '%';
        }

        // ===== Statistik Panjang per Kategori =====
        $('#stat-baik').html((lengthStats['Baik'] / 1000).toFixed(2) + ' Km');
        $('#pct-baik').html('(' + calcPct(lengthStats['Baik']) + ')');
        $('#stat-sedang').html((lengthStats['Sedang'] / 1000).toFixed(2) + ' Km');
        $('#pct-sedang').html('(' + calcPct(lengthStats['Sedang']) + ')');
        $('#stat-rusak-ringan').html((lengthStats['Rusak Ringan'] / 1000).toFixed(2) + ' Km');
        $('#pct-rusak-ringan').html('(' + calcPct(lengthStats['Rusak Ringan']) + ')');
        $('#stat-rusak-berat').html((lengthStats['Rusak Berat'] / 1000).toFixed(2) + ' Km');
        $('#pct-rusak-berat').html('(' + calcPct(lengthStats['Rusak Berat']) + ')');

        // ===== Rekomendasi Penanganan PKRMS =====
        const baik        = lengthStats['Baik'];
        const sedang      = lengthStats['Sedang'];
        const rusakRingan = lengthStats['Rusak Ringan'];
        const rusakBerat  = lengthStats['Rusak Berat'];
        const total       = baik + sedang + rusakRingan + rusakBerat;
        const perluPenanganan = sedang + rusakRingan + rusakBerat;

        function toKm(m) { return (m / 1000).toFixed(2) + ' Km'; }

        // Update panjang & persentase di masing-masing kartu rekomendasi
        $('#rek-panjang-baik').text(toKm(baik));
        $('#rek-pct-baik').text('(' + calcPct(baik) + ')');

        $('#rek-panjang-sedang').text(toKm(sedang));
        $('#rek-pct-sedang').text('(' + calcPct(sedang) + ')');

        $('#rek-panjang-rusak-ringan').text(toKm(rusakRingan));
        $('#rek-pct-rusak-ringan').text('(' + calcPct(rusakRingan) + ')');

        $('#rek-panjang-rusak-berat').text(toKm(rusakBerat));
        $('#rek-pct-rusak-berat').text('(' + calcPct(rusakBerat) + ')');

        // Update ringkasan total
        $('#rek-total-panjang').text(toKm(total));
        $('#rek-panjang-perlu').text(toKm(perluPenanganan));
        $('#rek-pct-perlu').text('(' + (total > 0 ? ((perluPenanganan / total) * 100).toFixed(1) : 0) + '%)');
        $('#rek-panjang-aman').text(toKm(baik));
        $('#rek-pct-aman').text('(' + calcPct(baik) + ')');

        console.log('🛣️ Rekomendasi PKRMS:', {
            baik: toKm(baik), sedang: toKm(sedang),
            rusak_ringan: toKm(rusakRingan), rusak_berat: toKm(rusakBerat),
            total: toKm(total), perlu_penanganan: toKm(perluPenanganan)
        });
    }

    loadKaliwatesData();
    loadPatrangData();
    loadSumbersariData();
});
</script>
@endpush