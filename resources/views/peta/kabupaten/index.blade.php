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
                    
                    <!-- ✅ FILTER KECAMATAN -->
                    <div id="kecamatanFilter" style="position: absolute; top: 10px; left: 10px; z-index: 1000; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.15); max-height: 70vh; overflow-y: auto; max-width: 250px;">
                        <h6 style="margin: 0 0 10px 0; font-weight: bold; font-size: 13px;">
                            <i class="fas fa-map-marker-alt"></i> Filter Kecamatan
                        </h6>
                        
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

                    <!-- Filter Kategori SDI (Floating) -->
                    <div id="categoryFilter" style="position: absolute; top: 10px; right: 10px; z-index: 1000; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.15);">
                        <h6 style="margin: 0 0 10px 0; font-weight: bold; font-size: 13px;">
                            <i class="fas fa-filter"></i> Filter Kategori SDI
                        </h6>
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
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1" style="border-left: 4px solid #2ecc71;">
                                        <div class="card-icon" style="background-color: #2ecc71;">
                                            <i class="fas fa-road"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Baik</h4>
                                            </div>
                                            <div class="card-body" id="stat-baik">
                                                <span class="text-muted">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1" style="border-left: 4px solid #f1c40f;">
                                        <div class="card-icon" style="background-color: #f1c40f;">
                                            <i class="fas fa-road"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Sedang</h4>
                                            </div>
                                            <div class="card-body" id="stat-sedang">
                                                <span class="text-muted">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1" style="border-left: 4px solid #e67e22;">
                                        <div class="card-icon" style="background-color: #e67e22;">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Rusak Ringan</h4>
                                            </div>
                                            <div class="card-body" id="stat-rusak-ringan">
                                                <span class="text-muted">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                    <div class="card card-statistic-1" style="border-left: 4px solid #e74c3c;">
                                        <div class="card-icon" style="background-color: #e74c3c;">
                                            <i class="fas fa-times-circle"></i>
                                        </div>
                                        <div class="card-wrap">
                                            <div class="card-header">
                                                <h4>Rusak Berat</h4>
                                            </div>
                                            <div class="card-body" id="stat-rusak-berat">
                                                <span class="text-muted">-</span>
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
    
    .legend {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        line-height: 1.8;
    }
    
    .legend h4 {
        margin: 0 0 10px 0;
        font-size: 14px;
        font-weight: bold;
        color: #333;
    }
    
    .legend-item {
        margin: 5px 0;
        display: flex;
        align-items: center;
        font-size: 12px;
    }
    
    .legend-color {
        display: inline-block;
        width: 25px;
        height: 4px;
        margin-right: 8px;
        border-radius: 2px;
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
</style>
@endpush

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ==================== ✅ JAVASCRIPT DENGAN INFO CARD ====================

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
                    addLegend();
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
     * Update statistik panjang
     */
    function updateLengthStatistics(lengthStats) {
        $('#stat-baik').html((lengthStats['Baik'] / 1000).toFixed(2) + ' KM');
        $('#stat-sedang').html((lengthStats['Sedang'] / 1000).toFixed(2) + ' KM');
        $('#stat-rusak-ringan').html((lengthStats['Rusak Ringan'] / 1000).toFixed(2) + ' KM');
        $('#stat-rusak-berat').html((lengthStats['Rusak Berat'] / 1000).toFixed(2) + ' KM');
    }

    /**
     * Add legend
     */
    let legendControl = null;
    function addLegend() {
        if (legendControl) {
            map.removeControl(legendControl);
        }

        legendControl = L.control({ position: 'bottomright' });
        
        legendControl.onAdd = function() {
            let div = L.DomUtil.create('div', 'legend');
            
            div.innerHTML = '<h4><i class="fas fa-info-circle"></i> Kategori SDI</h4>';
            div.innerHTML += '<small style="color: #666; display: block; margin-bottom: 10px;">Surface Distress Index</small>';
            
            const categories = ['Baik', 'Sedang', 'Rusak Ringan', 'Rusak Berat'];
            
            categories.forEach(category => {
                div.innerHTML += `
                    <div class="legend-item">
                        <span class="legend-color" style="background: ${sdiColors[category]};"></span>
                        <span><strong>${category}</strong></span>
                    </div>
                `;
            });
            
            return div;
        };
        
        legendControl.addTo(map);
    }
});
</script>
@endpush