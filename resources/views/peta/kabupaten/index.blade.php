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

                        <!-- Kaliwates -->
                        {{-- ✅ PERUBAHAN 1: Default UNCHECKED (tidak ada attribute 'checked') --}}
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

                        <!-- Patrang -->
                        {{-- ✅ PERUBAHAN 1: Default UNCHECKED --}}
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

                        <!-- Sumbersari -->
                        {{-- ✅ PERUBAHAN 1: Default UNCHECKED --}}
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

            <!-- ✅ CARD INFORMASI DATA (BARU) -->
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
                            <!-- ✅ BARIS 1 - Baik & Sedang -->
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
                                            {{-- ✅ PERUBAHAN 2: Tambah elemen persentase di sebelah panjang --}}
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
                                            {{-- ✅ PERUBAHAN 2: Tambah elemen persentase --}}
                                            <div class="card-body d-flex align-items-baseline" id="stat-sedang-wrap">
                                                <span id="stat-sedang"><span class="text-muted">-</span></span>
                                                <span id="pct-sedang" class="ml-2" style="font-size: 14px; font-weight: 500; color: #d4ac0d;"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ✅ BARIS 2 - Rusak Ringan & Rusak Berat -->
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
                                            {{-- ✅ PERUBAHAN 2: Tambah elemen persentase --}}
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
                                            {{-- ✅ PERUBAHAN 2: Tambah elemen persentase --}}
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

            <!-- Card Estimasi Biaya Perbaikan -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-calculator"></i> Estimasi Biaya Perbaikan</h4>
                            <div class="card-header-action">
                                <span class="badge badge-info">Harga per m² (lebar jalan aktual per ruas)</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Info Box -->
                            <div class="alert alert-info mb-3" style="border-left: 4px solid #17a2b8;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="alert-heading" style="color: #0c5460;">
                                            <i class="fas fa-info-circle"></i> Asumsi Perhitungan
                                        </h6>
                                        <div class="row" style="font-size: 13px; color: #0c5460;">
                                            <div class="col-md-3">
                                                <strong>Lebar Jalan:</strong> Aktual per ruas (3m - 7m)
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Sedang:</strong> Rp 600.000/m²
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Rusak Ringan:</strong> Rp 800.000/m²
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Rusak Berat:</strong> Rp 1.000.000/m²
                                            </div>
                                        </div>
                                        <small class="d-block mt-2" style="color: #856404;">
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            <strong>Catatan:</strong> Kondisi "Baik" tidak memerlukan perbaikan. Estimasi menggunakan lebar jalan aktual dari database (default 6m jika data tidak tersedia). Estimasi ini adalah perhitungan kasar dan dapat berbeda dengan harga aktual di lapangan.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- ✅ LAYOUT 2×2 (2 baris, 2 kolom per baris) -->
                            <div class="row">
                                <!-- BARIS 1 -->
                                <!-- Sedang -->
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div class="card card-statistic-1" style="border-left: 4px solid #f1c40f;">
                                        <div class="card-icon" style="background-color: #f1c40f;">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Sedang (Rp 600.000/m²)</h4>
                                            </div>
                                            <div class="card-body">
                                                <div id="cost-sedang" style="font-size: 18px; font-weight: 700; color: #333;">
                                                    <span class="text-muted">-</span>
                                                </div>
                                                <small class="text-muted" id="area-sedang">Luas: - m²</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Rusak Ringan -->
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div class="card card-statistic-1" style="border-left: 4px solid #e67e22;">
                                        <div class="card-icon" style="background-color: #e67e22;">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Rusak Ringan (Rp 800.000/m²)</h4>
                                            </div>
                                            <div class="card-body">
                                                <div id="cost-rusak-ringan" style="font-size: 18px; font-weight: 700; color: #333;">
                                                    <span class="text-muted">-</span>
                                                </div>
                                                <small class="text-muted" id="area-rusak-ringan">Luas: - m²</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- BARIS 2 -->
                                <!-- Rusak Berat -->
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div class="card card-statistic-1" style="border-left: 4px solid #e74c3c;">
                                        <div class="card-icon" style="background-color: #e74c3c;">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Rusak Berat (Rp 1.000.000/m²)</h4>
                                            </div>
                                            <div class="card-body">
                                                <div id="cost-rusak-berat" style="font-size: 18px; font-weight: 700; color: #333;">
                                                    <span class="text-muted">-</span>
                                                </div>
                                                <small class="text-muted" id="area-rusak-berat">Luas: - m²</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Biaya -->
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div class="card card-statistic-1" style="border-left: 4px solid #6777ef; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <div class="card-icon" style="background-color: rgba(255,255,255,0.3);">
                                            <i class="fas fa-calculator" style="color: white;"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4 style="color: white; opacity: 0.9;">Total Estimasi Biaya</h4>
                                            </div>
                                            <div class="card-body">
                                                <div id="cost-total" style="font-size: 18px; font-weight: 700; color: white;">
                                                    <span style="opacity: 0.7;">-</span>
                                                </div>
                                                <small style="color: white; opacity: 0.8;" id="area-total">Total Luas: - m²</small>
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

    /* Styling untuk Card Statistik */
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

    /* Style untuk kecamatan checkbox */
    #kecamatanList .form-check {
        padding-left: 5px;
    }

    #kecamatanList .form-check-label {
        font-size: 11px;
        font-weight: 500;
    }

    /* ✅ Animation untuk Info Card */
    #infoDataCard {
        animation: slideUp 0.5s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ✅ Smooth animation untuk toggle filters */
    #kecamatanFilter, #categoryFilter {
        transition: all 0.3s ease-in-out;
    }
</style>
@endpush

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ==================== ✅ JAVASCRIPT DENGAN TOGGLE FILTERS ====================

document.addEventListener("DOMContentLoaded", function () {
    // Initialize map
    let map = L.map('map').setView([-8.172, 113.687], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Warna SDI
    const sdiColors = {
        'Baik': '#2ecc71',
        'Sedang': '#f1c40f',
        'Rusak Ringan': '#e67e22',
        'Rusak Berat': '#e74c3c',
        'Tidak Ada Data': '#95a5a6'
    };

    // Store data
    let allSegments = [];
    let polylines = [];
    let selectedCategories = ['Baik', 'Sedang', 'Rusak Ringan', 'Rusak Berat'];
    let kecamatanData = [];
    let currentStats = null;

    const selectedYear = {{ session('selected_year', date('Y')) }};
    
    // ✅ Load list kecamatan saat halaman load
    loadKecamatanList();

    // ==================== ✅ DATA DRAINASE 3 KECAMATAN ====================

    // Variable untuk data drainase
    let dataKaliwates = [];
    let dataPatrang = [];
    let dataSumbersari = [];

    let kaliwatesLayer = L.layerGroup(); 
    let patrangLayer = L.layerGroup(); 
    let sumbersariLayer = L.layerGroup();

    // ✅ PERUBAHAN 1: Default semua false — layer TIDAK ditampilkan saat halaman dibuka
    let showKaliwates = false;
    let showPatrang = false;
    let showSumbersari = false;

    // ✅ LOAD DATA KALIWATES
    function loadKaliwatesData() {
        fetch('/data/kaliwates.json')
            .then(response => {
                if (!response.ok) throw new Error('File JSON Kaliwates tidak ditemukan');
                return response.json();
            })
            .then(geojson => {
                dataKaliwates = geojson.features;
                renderKaliwatesLayer();
                console.log(`✅ ${geojson.features.length} features Kaliwates berhasil dimuat`);
            })
            .catch(error => {
                console.error('❌ Error loading Kaliwates:', error);
                console.warn('💡 Pastikan file kaliwates.json ada di public/data/');
            });
    }

    // ✅ LOAD DATA PATRANG
    function loadPatrangData() {
        fetch('/data/patrang.json')
            .then(response => {
                if (!response.ok) throw new Error('File JSON Patrang tidak ditemukan');
                return response.json();
            })
            .then(geojson => {
                dataPatrang = geojson.features;
                renderPatrangLayer();
                console.log(`✅ ${geojson.features.length} features Patrang berhasil dimuat`);
            })
            .catch(error => {
                console.error('❌ Error loading Patrang:', error);
                console.warn('💡 Pastikan file patrang.json ada di public/data/');
            });
    }

    // ✅ LOAD DATA SUMBERSARI
    function loadSumbersariData() {
        fetch('/data/sumbersari.json')
            .then(response => {
                if (!response.ok) throw new Error('File JSON Sumbersari tidak ditemukan');
                return response.json();
            })
            .then(geojson => {
                dataSumbersari = geojson.features;
                renderSumbersariLayer();
                console.log(`✅ ${geojson.features.length} features Sumbersari berhasil dimuat`);
            })
            .catch(error => {
                console.error('❌ Error loading Sumbersari:', error);
                console.warn('💡 Pastikan file sumbersari.json ada di public/data/');
            });
    }

    // ✅ RENDER KALIWATES LAYER (Biru Muda)
    function renderKaliwatesLayer() {
        kaliwatesLayer.clearLayers();
        
        const kaliwatesStyle = {
            color: '#2196F3',
            weight: 4,
            opacity: 0.8,
            fillColor: '#2196F3',
            fillOpacity: 0.3
        };
        
        const kaliwatesIcon = L.divIcon({
            className: 'custom-div-icon',
            html: '<div style="background-color: #2196F3; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3); font-size: 12px; color: white; font-weight: bold;">K</div>',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });
        
        dataKaliwates.forEach(feature => {
            const props = feature.properties;
            const geom = feature.geometry;
            
            const popupContent = `
                <div style="font-family: Arial, sans-serif;">
                    <strong style="font-size: 14px; color: #2196F3;">
                        🌊 ${props.name}
                    </strong>
                    <hr style="margin: 8px 0; border-color: #ddd;">
                    <table style="width: 100%; font-size: 12px;">
                        <tr>
                            <td style="padding: 3px 0;"><strong>Kecamatan:</strong></td>
                            <td>Kaliwates</td>
                        </tr>
                        ${props.keterangan ? `
                        <tr>
                            <td style="padding: 3px 0; vertical-align: top;"><strong>Keterangan:</strong></td>
                            <td>${props.keterangan}</td>
                        </tr>
                        ` : ''}
                        <tr>
                            <td style="padding: 3px 0;"><strong>Tipe:</strong></td>
                            <td>${geom.type}</td>
                        </tr>
                    </table>
                </div>
            `;
            
            if (geom.type === 'Point') {
                const marker = L.marker([geom.coordinates[1], geom.coordinates[0]], { 
                    icon: kaliwatesIcon 
                }).bindPopup(popupContent);
                kaliwatesLayer.addLayer(marker);
                
            } else if (geom.type === 'LineString') {
                const coords = geom.coordinates.map(c => [c[1], c[0]]);
                const polyline = L.polyline(coords, kaliwatesStyle).bindPopup(popupContent);
                kaliwatesLayer.addLayer(polyline);
                
            } else if (geom.type === 'Polygon') {
                const coords = geom.coordinates[0].map(c => [c[1], c[0]]);
                const polygon = L.polygon(coords, kaliwatesStyle).bindPopup(popupContent);
                kaliwatesLayer.addLayer(polygon);
            }
        });
        
        // ✅ PERUBAHAN 1: Hanya tambahkan ke map kalau showKaliwates = true
        if (showKaliwates) kaliwatesLayer.addTo(map);
        console.log(`📍 ${dataKaliwates.length} features Kaliwates dimuat (${showKaliwates ? 'ditampilkan' : 'tersembunyi'})`);
    }

    // ✅ RENDER PATRANG LAYER (Biru Tua)
    function renderPatrangLayer() {
        patrangLayer.clearLayers();
        
        const patrangStyle = {
            color: '#1976D2',
            weight: 4,
            opacity: 0.8,
            fillColor: '#1976D2',
            fillOpacity: 0.3
        };
        
        const patrangIcon = L.divIcon({
            className: 'custom-div-icon',
            html: '<div style="background-color: #1976D2; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3); font-size: 12px; color: white; font-weight: bold;">P</div>',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });
        
        dataPatrang.forEach(feature => {
            const props = feature.properties;
            const geom = feature.geometry;
            
            const popupContent = `
                <div style="font-family: Arial, sans-serif;">
                    <strong style="font-size: 14px; color: #1976D2;">
                        🌊 ${props.name}
                    </strong>
                    <hr style="margin: 8px 0; border-color: #ddd;">
                    <table style="width: 100%; font-size: 12px;">
                        <tr>
                            <td style="padding: 3px 0;"><strong>Kecamatan:</strong></td>
                            <td>Patrang</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;"><strong>Tipe:</strong></td>
                            <td>${geom.type}</td>
                        </tr>
                    </table>
                </div>
            `;
            
            if (geom.type === 'Point') {
                const marker = L.marker([geom.coordinates[1], geom.coordinates[0]], { 
                    icon: patrangIcon 
                }).bindPopup(popupContent);
                patrangLayer.addLayer(marker);
                
            } else if (geom.type === 'LineString') {
                const coords = geom.coordinates.map(c => [c[1], c[0]]);
                const polyline = L.polyline(coords, patrangStyle).bindPopup(popupContent);
                patrangLayer.addLayer(polyline);
                
            } else if (geom.type === 'Polygon') {
                const coords = geom.coordinates[0].map(c => [c[1], c[0]]);
                const polygon = L.polygon(coords, patrangStyle).bindPopup(popupContent);
                patrangLayer.addLayer(polygon);
            }
        });
        
        // ✅ PERUBAHAN 1: Hanya tambahkan ke map kalau showPatrang = true
        if (showPatrang) patrangLayer.addTo(map);
        console.log(`📍 ${dataPatrang.length} features Patrang dimuat (${showPatrang ? 'ditampilkan' : 'tersembunyi'})`);
    }

    // ✅ RENDER SUMBERSARI LAYER (Hijau)
    function renderSumbersariLayer() {
        sumbersariLayer.clearLayers();
        
        const sumbersariStyle = {
            color: '#388E3C',
            weight: 4,
            opacity: 0.8,
            fillColor: '#388E3C',
            fillOpacity: 0.3
        };
        
        const sumbersariIcon = L.divIcon({
            className: 'custom-div-icon',
            html: '<div style="background-color: #388E3C; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3); font-size: 12px; color: white; font-weight: bold;">S</div>',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });
        
        dataSumbersari.forEach(feature => {
            const props = feature.properties;
            const geom = feature.geometry;
            
            const popupContent = `
                <div style="font-family: Arial, sans-serif;">
                    <strong style="font-size: 14px; color: #388E3C;">
                        🌊 ${props.name}
                    </strong>
                    <hr style="margin: 8px 0; border-color: #ddd;">
                    <table style="width: 100%; font-size: 12px;">
                        <tr>
                            <td style="padding: 3px 0;"><strong>Kecamatan:</strong></td>
                            <td>Sumbersari</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;"><strong>Tipe:</strong></td>
                            <td>${geom.type}</td>
                        </tr>
                    </table>
                </div>
            `;
            
            if (geom.type === 'Point') {
                const marker = L.marker([geom.coordinates[1], geom.coordinates[0]], { 
                    icon: sumbersariIcon 
                }).bindPopup(popupContent);
                sumbersariLayer.addLayer(marker);
                
            } else if (geom.type === 'LineString') {
                const coords = geom.coordinates.map(c => [c[1], c[0]]);
                const polyline = L.polyline(coords, sumbersariStyle).bindPopup(popupContent);
                sumbersariLayer.addLayer(polyline);
                
            } else if (geom.type === 'Polygon') {
                const coords = geom.coordinates[0].map(c => [c[1], c[0]]);
                const polygon = L.polygon(coords, sumbersariStyle).bindPopup(popupContent);
                sumbersariLayer.addLayer(polygon);
            }
        });
        
        // ✅ PERUBAHAN 1: Hanya tambahkan ke map kalau showSumbersari = true
        if (showSumbersari) sumbersariLayer.addTo(map);
        console.log(`📍 ${dataSumbersari.length} features Sumbersari dimuat (${showSumbersari ? 'ditampilkan' : 'tersembunyi'})`);
    }

    // ✅ TOGGLE FUNCTIONS
    function toggleKaliwatesLayer() {
        if (showKaliwates && !map.hasLayer(kaliwatesLayer)) {
            kaliwatesLayer.addTo(map);
        } else if (!showKaliwates && map.hasLayer(kaliwatesLayer)) {
            map.removeLayer(kaliwatesLayer);
        }
    }

    function togglePatrangLayer() {
        if (showPatrang && !map.hasLayer(patrangLayer)) {
            patrangLayer.addTo(map);
        } else if (!showPatrang && map.hasLayer(patrangLayer)) {
            map.removeLayer(patrangLayer);
        }
    }

    function toggleSumbersariLayer() {
        if (showSumbersari && !map.hasLayer(sumbersariLayer)) {
            sumbersariLayer.addTo(map);
        } else if (!showSumbersari && map.hasLayer(sumbersariLayer)) {
            map.removeLayer(sumbersariLayer);
        }
    }

    // ✅ UPDATE LEGEND COUNT - dihapus (legend sudah tidak digunakan)

    // ==================== ✅ TOGGLE BUTTON EVENT LISTENERS ====================
    
    // Toggle Kecamatan Filter
    $('#toggleKecamatanBtn').on('click', function() {
        $('#kecamatanFilter').slideToggle(300);
        $(this).toggleClass('active');
    });

    // Close Kecamatan Filter
    $('#closeKecamatanFilter').on('click', function() {
        $('#kecamatanFilter').slideUp(300);
        $('#toggleKecamatanBtn').removeClass('active');
    });

    // Toggle Category Filter
    $('#toggleCategoryBtn').on('click', function() {
        $('#categoryFilter').slideToggle(300);
        $(this).toggleClass('active');
    });

    // Close Category Filter
    $('#closeCategoryFilter').on('click', function() {
        $('#categoryFilter').slideUp(300);
        $('#toggleCategoryBtn').removeClass('active');
    });

    // ==================== EVENT LISTENERS ====================
    
    // Load data peta berdasarkan kecamatan yang dipilih
    $('#loadMapData').on('click', function() {
        const selectedKecamatan = getSelectedKecamatan();
        
        if (selectedKecamatan.length === 0) {
            alert('⚠️ Pilih minimal 1 kecamatan terlebih dahulu!');
            return;
        }
        
        loadMapData(selectedYear, selectedKecamatan);
    });

    // Select All / Deselect All
    $('#selectAllKecamatan').on('click', function() {
        const allChecked = $('.kecamatan-checkbox:checked').length === $('.kecamatan-checkbox').length;
        
        $('.kecamatan-checkbox').prop('checked', !allChecked);
        
        // Update text button
        if (allChecked) {
            $(this).html('<i class="fas fa-check-double"></i> Pilih Semua');
        } else {
            $(this).html('<i class="fas fa-times"></i> Batalkan Semua');
        }
    });

    // Filter kategori
    $('#applyFilter').on('click', function() {
        applyFilter();
    });

    $('#resetFilter').on('click', function() {
        $('.category-checkbox').prop('checked', true);
        applyFilter();
    });

    // ✅ TOGGLE KALIWATES
    $('#toggleKaliwates').on('change', function() {
        showKaliwates = $(this).is(':checked');
        toggleKaliwatesLayer();
    });

    // ✅ TOGGLE PATRANG
    $('#togglePatrang').on('change', function() {
        showPatrang = $(this).is(':checked');
        togglePatrangLayer();
    });

    // ✅ TOGGLE SUMBERSARI
    $('#toggleSumbersari').on('change', function() {
        showSumbersari = $(this).is(':checked');
        toggleSumbersariLayer();
    });

    // ==================== FUNCTIONS ====================

    /**
     * ✅ Load list kecamatan dari API
     */
    function loadKecamatanList() {
        $('#kecamatanLoading').show();
        $('#kecamatanList').hide();

        $.ajax({
            url: '/api/alignment/kecamatan-list',
            type: 'GET',
            data: { year: selectedYear },
            success: function(response) {
                console.log('✅ Kecamatan loaded:', response);

                if (response.success && response.data.length > 0) {
                    kecamatanData = response.data;
                    renderKecamatanCheckboxes(response.data);
                    $('#kecamatanLoading').hide();
                    $('#kecamatanList').show();
                } else {
                    $('#kecamatanLoading').html('<small class="text-danger">Tidak ada data kecamatan</small>');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading kecamatan:', error);
                $('#kecamatanLoading').html('<small class="text-danger">Gagal memuat data</small>');
            }
        });
    }

    /**
     * ✅ Render checkbox kecamatan
     */
    function renderKecamatanCheckboxes(data) {
        let html = '';
        
        data.forEach(function(kec) {
            html += `
                <div class="form-check">
                    <input class="form-check-input kecamatan-checkbox" 
                           type="checkbox" 
                           value="${kec.kecamatan_code}" 
                           id="kec_${kec.kecamatan_code}">
                    <label class="form-check-label" for="kec_${kec.kecamatan_code}">
                        ${kec.kecamatan_name}
                        <small class="text-muted d-block" style="font-size: 9px;">
                            ${kec.total_links} ruas | ${kec.total_segments} segmen
                        </small>
                    </label>
                </div>
            `;
        });
        
        $('#kecamatanList').html(html);
    }

    /**
     * ✅ Get kecamatan yang dipilih
     */
    function getSelectedKecamatan() {
        let selected = [];
        $('.kecamatan-checkbox:checked').each(function() {
            selected.push($(this).val());
        });
        return selected;
    }

    /**
     * ✅ Load map data dari API
     */
    function loadMapData(year, kecamatanCodes) {
        $.ajax({
            url: '/api/alignment/coords-sdi-by-kecamatan',
            type: 'GET',
            data: { 
                year: year,
                kecamatan_codes: kecamatanCodes 
            },
            timeout: 600000,
            beforeSend: function() {
                console.log('⏳ Mengambil data untuk kecamatan:', kecamatanCodes);
                
                $('#loadMapData').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm"></span> Memuat...');
                
                $('#total-links').html('<span class="spinner-border spinner-border-sm"></span> Loading...');
                
                polylines.forEach(p => map.removeLayer(p));
                polylines = [];

                // ✅ Hide info card saat loading
                $('#infoDataCard').fadeOut(200);
            },
            success: function(response) {
                console.log('✅ Data loaded:', response);
                
                // ✅ Update info card dengan stats
                if (response.stats) {
                    currentStats = response.stats;
                    updateInfoCard(response.stats);
                    
                    console.log(`📊 Statistik:
                        Total: ${response.stats.total}
                        Ditampilkan: ${response.stats.displayed}
                        Fallback: ${response.stats.fallback}
                        Skip: ${response.stats.skipped}
                    `);
                }

                if (response.success && response.data && response.data.length > 0) {
                    allSegments = response.data;
                    renderSegments();
                } else {
                    $('#total-links').html('Tidak ada data');
                    alert(response.message || 'Tidak ada data untuk kecamatan yang dipilih');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading data:', status, error);

                if (status === 'timeout') {
                    $('#total-links').html('Error: Timeout');
                    alert('Permintaan terlalu lama. Coba kurangi jumlah kecamatan yang dipilih.');
                } else if (xhr.status === 422) {
                    $('#total-links').html('Error: Validasi Gagal');
                    let errorMsg = 'Validasi gagal: ';
                    if (xhr.responseJSON && xhr.responseJSON.details) {
                        errorMsg += Object.values(xhr.responseJSON.details).flat().join(', ');
                    }
                    alert(errorMsg);
                } else if (xhr.status === 400) {
                    $('#total-links').html('Error: Bad Request');
                    alert(xhr.responseJSON?.message || 'Pilih minimal 1 kecamatan');
                } else if (xhr.status === 500) {
                    $('#total-links').html('Error: Server Error');
                    alert('Terjadi kesalahan di server. Silakan coba lagi atau hubungi administrator.');
                } else {
                    $('#total-links').html('Error: ' + error);
                    alert('Gagal memuat data peta. Silakan refresh halaman.');
                }
            },
            complete: function() {
                $('#loadMapData').prop('disabled', false)
                    .html('<i class="fas fa-map"></i> Tampilkan Peta');
            }
        });
    }

    /**
     * ✅ Update Info Card
     */
    function updateInfoCard(stats) {
        // Show card
        $('#infoDataCard').fadeIn(400);

        // Total segments
        $('#info-total-segments').text(stats.total);
        $('#info-displayed-segments').text(stats.displayed);
        $('#info-skipped-segments').text(stats.skipped);

        // Coordinate status
        $('#info-range-coords').text(stats.range_coords);
        $('#info-fallback-coords').text(stats.fallback);
        $('#info-no-coords').text(stats.skipped);

        // Success rate
        const successRate = parseFloat(stats.success_rate);
        $('#info-success-rate-big').text(stats.success_rate);
        $('#info-success-bar').css('width', successRate + '%');

        // Badge color
        let badgeClass = 'badge-success';
        let badgeText = 'Sangat Baik';
        
        if (successRate >= 90) {
            badgeClass = 'badge-success';
            badgeText = 'Sangat Baik';
        } else if (successRate >= 70) {
            badgeClass = 'badge-warning';
            badgeText = 'Perlu Perhatian';
        } else {
            badgeClass = 'badge-danger';
            badgeText = 'Perlu Perbaikan';
        }
        
        $('#info-success-badge').removeClass('badge-success badge-warning badge-danger')
            .addClass(badgeClass)
            .text(badgeText + ' (' + stats.success_rate + ')');

        // Warnings
        if (stats.skipped > 0) {
            const skipPercent = ((stats.skipped / stats.total) * 100).toFixed(1);
            let warningText = `${stats.skipped} segmen (${skipPercent}%) tidak memiliki koordinat yang valid dan tidak dapat ditampilkan di peta.`;
            
            if (skipPercent > 20) {
                warningText += ` <strong>Persentase ini cukup tinggi</strong>, disarankan untuk memeriksa kelengkapan data koordinat di database.`;
            }
            
            $('#info-warning-text').html(warningText);
            $('#info-warnings').fadeIn(300);
        } else {
            $('#info-warnings').hide();
        }

        // Smooth scroll ke info card
        $('html, body').animate({
            scrollTop: $('#infoDataCard').offset().top - 100
        }, 800);
    }

    /**
     * Apply filter kategori
     */
    function applyFilter() {
        selectedCategories = [];
        $('.category-checkbox:checked').each(function() {
            selectedCategories.push($(this).val());
        });

        console.log('🔍 Filter applied:', selectedCategories);
        renderSegments();
    }

    /**
     * Render segments berdasarkan filter
     */
    function renderSegments() {
        // Clear existing polylines
        polylines.forEach(p => map.removeLayer(p));
        polylines = [];

        let totalSegments = 0;
        let allBounds = [];

        // Statistik panjang
        let lengthStats = {
            'Baik': 0,
            'Sedang': 0,
            'Rusak Ringan': 0,
            'Rusak Berat': 0
        };

        // Statistik COUNT (untuk badge)
        let countStats = {
            'Baik': 0,
            'Sedang': 0,
            'Rusak Ringan': 0,
            'Rusak Berat': 0
        };

        // Filter data
        const filteredSegments = allSegments.filter(segment => 
            selectedCategories.includes(segment.category)
        );

        console.log(`📊 Rendering ${filteredSegments.length} dari ${allSegments.length} segments`);

        // Loop segments
        filteredSegments.forEach((segment) => {
            if (!segment.coords || segment.coords.length < 2) {
                console.warn('⚠️ Segment tidak punya koordinat atau koordinat kurang dari 2:', segment);
                return;
            }

            totalSegments++;
            countStats[segment.category]++;
            
            let segmentLength = (segment.chainage_to - segment.chainage_from);
            lengthStats[segment.category] += segmentLength;
            
            let coords = segment.coords.map(c => [c.lat, c.lng]);
            let color = sdiColors[segment.category] || '#95a5a6';

            let polyline = L.polyline(coords, {
                color: color,
                weight: 6,
                opacity: 0.85,
                smoothFactor: 1
            }).addTo(map);

            polylines.push(polyline);

            const sdiValue = segment.sdi_final !== null ? segment.sdi_final.toFixed(2) : 'N/A';
            const categoryColor = color;
            const chainageFromKm = (segment.chainage_from / 1000).toFixed(3);
            const chainageToKm = (segment.chainage_to / 1000).toFixed(3);
            const segmentLengthM = segmentLength.toFixed(0);

            polyline.bindPopup(`
                <div style="font-family: Arial, sans-serif;">
                    <strong style="font-size: 16px; color: #333;">Ruas ${segment.link_no}</strong><br>
                    <div style="font-size: 13px; color: #666; margin-top: 4px; font-weight: 500;">
                        ${segment.link_name || 'Nama ruas tidak tersedia'}
                    </div>
                    <hr style="margin: 8px 0; border-color: #ddd;">
                    <table style="width: 100%; font-size: 12px;">
                        <tr>
                            <td style="padding: 3px 0;"><strong>Chainage:</strong></td>
                            <td>${chainageFromKm} - ${chainageToKm}<br>
                                <small style="color: #666;">(${segment.chainage_from.toFixed(0)} - ${segment.chainage_to.toFixed(0)} m)</small>
                            </td>
                        </tr>   
                        <tr>
                            <td style="padding: 3px 0;"><strong>Panjang:</strong></td>
                            <td><strong>${segmentLengthM} M</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;"><strong>Kategori:</strong></td>
                            <td>
                                <span style="background: ${categoryColor}; color: white; padding: 2px 8px; border-radius: 3px; font-weight: bold; font-size: 11px;">
                                    ${segment.category}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;"><strong>Nilai SDI:</strong></td>
                            <td><strong>${sdiValue}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;"><strong>Tahun:</strong></td>
                            <td>${segment.year} (ref: ${segment.reference_year || 'N/A'})</td>
                        </tr>
                    </table>
                </div>
            `);

            polyline.on('mouseover', function() {
                this.setStyle({ weight: 8, opacity: 1 });
            });

            polyline.on('mouseout', function() {
                this.setStyle({ weight: 6, opacity: 0.85 });
            });

            allBounds.push(...coords);
        });

        let uniqueLinks = [...new Set(filteredSegments
            .filter(s => s.coords && s.coords.length >= 2)
            .map(s => s.link_no))];
        
        $('#total-links').html(
            `${uniqueLinks.length} Ruas | ${totalSegments} Segmen | 
            <span style="color: #2ecc71">B:${countStats['Baik']}</span> 
            <span style="color: #FFD700">S:${countStats['Sedang']}</span> 
            <span style="color: #FFA500">RR:${countStats['Rusak Ringan']}</span> 
            <span style="color: #e74c3c">RB:${countStats['Rusak Berat']}</span>`
        );

        updateLengthStatistics(lengthStats);

        if (allBounds.length > 0) {
            let bounds = L.latLngBounds(allBounds);
            map.fitBounds(bounds, { padding: [50, 50] });
        } else {
            console.warn('⚠️ Tidak ada koordinat untuk di-fit bounds');
        }
    }

    /**
     * ✅ PERUBAHAN 2: Update statistik panjang + persentase per kategori
     */
    function updateLengthStatistics(lengthStats) {
        // ===== Hitung total panjang dari semua kategori yang ada di data (bukan hanya yang difilter) =====
        // Supaya persentase konsisten terhadap total keseluruhan data yang dimuat
        let totalAllLength = Object.values(lengthStats).reduce((sum, val) => sum + val, 0);

        // Helper hitung persentase
        function calcPct(value) {
            if (totalAllLength === 0) return '0%';
            return ((value / totalAllLength) * 100).toFixed(1) + '%';
        }

        // Update panjang + persentase
        $('#stat-baik').html((lengthStats['Baik'] / 1000).toFixed(2) + ' Km');
        $('#pct-baik').html('(' + calcPct(lengthStats['Baik']) + ')');

        $('#stat-sedang').html((lengthStats['Sedang'] / 1000).toFixed(2) + ' Km');
        $('#pct-sedang').html('(' + calcPct(lengthStats['Sedang']) + ')');

        $('#stat-rusak-ringan').html((lengthStats['Rusak Ringan'] / 1000).toFixed(2) + ' Km');
        $('#pct-rusak-ringan').html('(' + calcPct(lengthStats['Rusak Ringan']) + ')');

        $('#stat-rusak-berat').html((lengthStats['Rusak Berat'] / 1000).toFixed(2) + ' Km');
        $('#pct-rusak-berat').html('(' + calcPct(lengthStats['Rusak Berat']) + ')');

        // ===== ESTIMASI BIAYA DENGAN LEBAR JALAN AKTUAL =====
        const HARGA_PER_M2 = {
            'Sedang': 600000,
            'Rusak Ringan': 800000,
            'Rusak Berat': 1000000
        };

        let luasSedang = 0;
        let luasRusakRingan = 0;
        let luasRusakBerat = 0;

        const filteredSegments = allSegments.filter(segment => 
            selectedCategories.includes(segment.category)
        );

        filteredSegments.forEach(segment => {
            const panjang = segment.chainage_to - segment.chainage_from;
            const lebar = segment.pave_width || 6;
            const luas = panjang * lebar;

            if (segment.category === 'Sedang') {
                luasSedang += luas;
            } else if (segment.category === 'Rusak Ringan') {
                luasRusakRingan += luas;
            } else if (segment.category === 'Rusak Berat') {
                luasRusakBerat += luas;
            }
        });

        const biayaSedang = luasSedang * HARGA_PER_M2['Sedang'];
        const biayaRusakRingan = luasRusakRingan * HARGA_PER_M2['Rusak Ringan'];
        const biayaRusakBerat = luasRusakBerat * HARGA_PER_M2['Rusak Berat'];
        const biayaTotal = biayaSedang + biayaRusakRingan + biayaRusakBerat;

        function formatRupiah(angka) {
            if (angka === 0) return 'Rp 0';
            return 'Rp ' + angka.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        // Update UI - Biaya
        $('#cost-sedang').html(formatRupiah(biayaSedang));
        $('#cost-rusak-ringan').html(formatRupiah(biayaRusakRingan));
        $('#cost-rusak-berat').html(formatRupiah(biayaRusakBerat));
        $('#cost-total').html(formatRupiah(biayaTotal));

        // Update UI - Luas
        $('#area-sedang').html(`Luas: ${luasSedang.toLocaleString('id-ID', {maximumFractionDigits: 0})} m²`);
        $('#area-rusak-ringan').html(`Luas: ${luasRusakRingan.toLocaleString('id-ID', {maximumFractionDigits: 0})} m²`);
        $('#area-rusak-berat').html(`Luas: ${luasRusakBerat.toLocaleString('id-ID', {maximumFractionDigits: 0})} m²`);
        $('#area-total').html(`Total Luas: ${(luasSedang + luasRusakRingan + luasRusakBerat).toLocaleString('id-ID', {maximumFractionDigits: 0})} m²`);

        console.log('💰 Estimasi Biaya (dengan pave_width aktual):', {
            biaya_sedang: formatRupiah(biayaSedang),
            biaya_rusak_ringan: formatRupiah(biayaRusakRingan),
            biaya_rusak_berat: formatRupiah(biayaRusakBerat),
            biaya_total: formatRupiah(biayaTotal)
        });
    }

    loadKaliwatesData();
    loadPatrangData();
    loadSumbersariData();
});
</script>
@endpush