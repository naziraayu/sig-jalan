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
                            <span class="badge badge-secondary" id="total-links">Loading...</span>
                        </div>
                    </div>
                    <div class="card-body p-0" style="position: relative;">
                        <!-- Filter Kategori SDI (Floating) -->
                        <div id="categoryFilter" style="position: absolute; top: 10px; left: 10px; z-index: 1000; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.15);">
                            <h6 style="margin: 0 0 10px 0; font-weight: bold; font-size: 13px;">
                                <i class="fas fa-filter"></i> Filter Kategori
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
                                                    <span class="spinner-border spinner-border-sm" role="status"></span>
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
                                                    <span class="spinner-border spinner-border-sm" role="status"></span>
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
                                                    <span class="spinner-border spinner-border-sm" role="status"></span>
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
                                                    <span class="spinner-border spinner-border-sm" role="status"></span>
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

        #categoryFilter {
            max-width: 200px;
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
    </style>
    @endpush

    @push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Initialize map
        let map = L.map('map').setView([-8.172, 113.687], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Warna yang PASTI sesuai kategori SDI
        const sdiColors = {
            'Baik': '#2ecc71',           // Hijau
            'Sedang': '#f1c40f',         // Kuning
            'Rusak Ringan': '#e67e22',   // Orange
            'Rusak Berat': '#e74c3c',    // Merah
            'Tidak Ada Data': '#95a5a6'  // Abu-abu
        };

        // Store semua segment data dan polylines
        let allSegments = [];
        let polylines = [];
        let selectedCategories = ['Baik', 'Sedang', 'Rusak Ringan', 'Rusak Berat'];

        // Ambil tahun dari session PHP
        const selectedYear = {{ session('selected_year', date('Y')) }};
        
        // Load data pertama kali
        loadMapData(selectedYear);

        // Event listener untuk filter kategori
        document.getElementById('applyFilter').addEventListener('click', function() {
            applyFilter();
        });

        document.getElementById('resetFilter').addEventListener('click', function() {
            // Check semua checkbox
            document.querySelectorAll('.category-checkbox').forEach(cb => {
                cb.checked = true;
            });
            applyFilter();
        });

        /**
        * Load map data dari API
        */
function loadMapData(year) {
    document.getElementById('total-links').textContent = 'Loading...';

    $.ajax({
        url: `/api/alignment/coords-sdi`,
        type: 'GET',
        data: { year: year },
        dataType: 'json',
        timeout: 0, 
        beforeSend: function() {
            console.log('⏳ Mengambil data tahun', year);
        },
        success: function(data) {
            console.log('✅ Data loaded:', data);

            if (!data || data.length === 0) {
                console.warn("⚠️ Data koordinat kosong untuk tahun " + year);
                $('#total-links').text('Tidak ada data');
                return;
            }

            // Simpan semua data
            allSegments = data;

            // Render dengan filter default (semua kategori)
            renderSegments();

            // Add legend
            addLegend();
        },
        error: function(xhr, status, error) {
            console.error('❌ Error loading data:', status, error);

            if (status === 'timeout') {
                $('#total-links').text('Error: Timeout');
                alert('Permintaan terlalu lama (timeout). Silakan coba lagi.');
            } else if (xhr.status === 500) {
                $('#total-links').text('Error: Server');
                alert('Terjadi kesalahan di server. Hubungi administrator.');
            } else if (xhr.status === 404) {
                $('#total-links').text('Error: 404 Not Found');
                alert('API tidak ditemukan. Pastikan route benar.');
            } else {
                $('#total-links').text('Error: ' + error);
                alert('Gagal memuat data peta. Silakan refresh halaman.');
            }
        },
        complete: function() {
            console.log('✅ Request selesai (ajax complete)');
        }
    });
}


        /**
        * Apply filter berdasarkan kategori yang dipilih
        */
        function applyFilter() {
            // Ambil kategori yang dicentang
            selectedCategories = [];
            document.querySelectorAll('.category-checkbox:checked').forEach(cb => {
                selectedCategories.push(cb.value);
            });

            console.log('🔍 Filter applied:', selectedCategories);

            // Render ulang
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

            // ✅ TAMBAHAN: Statistik panjang per kategori
            let lengthStats = {
                'Baik': 0,
                'Sedang': 0,
                'Rusak Ringan': 0,
                'Rusak Berat': 0
            };

            // Filter data berdasarkan kategori yang dipilih
            const filteredSegments = allSegments.filter(segment => 
                selectedCategories.includes(segment.category)
            );

            console.log(`📊 Rendering ${filteredSegments.length} dari ${allSegments.length} segments`);

            // Loop per segmen yang sudah difilter
            filteredSegments.forEach((segment) => {
                if (!segment.coords || segment.coords.length < 2) {
                    console.warn('⚠️ Segment skipped - insufficient coordinates:', segment);
                    return;
                }

                totalSegments++;
                
                // ✅ TAMBAHAN: Hitung panjang segmen dalam km
                let segmentLength = (segment.chainage_to - segment.chainage_from);
                lengthStats[segment.category] += segmentLength;
                
                // Convert coords to Leaflet format
                let coords = segment.coords.map(c => [c.lat, c.lng]);
                let color = sdiColors[segment.category] || '#95a5a6';

                console.log(`🎨 Segment ${segment.link_no}: ${segment.category} = ${color}`);

                // Gambar polyline per segmen
                let polyline = L.polyline(coords, {
                    color: color,
                    weight: 6,
                    opacity: 0.85,
                    smoothFactor: 1
                }).addTo(map);

                polylines.push(polyline);

                // Popup info dengan styling lebih baik
                const sdiValue = segment.sdi_final !== null ? segment.sdi_final.toFixed(2) : 'N/A';
                const categoryColor = color;

                // Convert dari meter ke kilometer untuk display
                const chainageFromKm = (segment.chainage_from / 1000).toFixed(3);
                const chainageToKm = (segment.chainage_to / 1000).toFixed(3);
                const segmentLengthM = segmentLength.toFixed(0); // dalam meter

                polyline.bindPopup(`
                    <div style="font-family: Arial, sans-serif;">
                        <strong style="font-size: 16px; color: #333;">Ruas ${segment.link_no}</strong><br>
                        <hr style="margin: 8px 0; border-color: #ddd;">
                        <table style="width: 100%; font-size: 12px;">
                            <tr>
                                <td style="padding: 3px 0;"><strong>Chainage:</strong></td>
                                <td>${chainageFromKm} - ${chainageToKm} M<br>
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
                                <td>${segment.year}</td>
                            </tr>
                        </table>
                    </div>
                `);

                // Add hover effect
                polyline.on('mouseover', function() {
                    this.setStyle({ weight: 8, opacity: 1 });
                });

                polyline.on('mouseout', function() {
                    this.setStyle({ weight: 6, opacity: 0.85 });
                });

                allBounds.push(...coords);
            });

            // Update stats
            let uniqueLinks = [...new Set(filteredSegments.map(s => s.link_no))];
            
            // Hitung jumlah per kategori
            const stats = {
                'Baik': filteredSegments.filter(s => s.category === 'Baik').length,
                'Sedang': filteredSegments.filter(s => s.category === 'Sedang').length,
                'Rusak Ringan': filteredSegments.filter(s => s.category === 'Rusak Ringan').length,
                'Rusak Berat': filteredSegments.filter(s => s.category === 'Rusak Berat').length,
            };

            document.getElementById('total-links').innerHTML = 
                `${uniqueLinks.length} Ruas | ${totalSegments} Segmen | 
                <span style="color: #2ecc71">B:${stats['Baik']}</span> 
                <span style="color: #f1c40f">S:${stats['Sedang']}</span> 
                <span style="color: #e67e22">RR:${stats['Rusak Ringan']}</span> 
                <span style="color: #e74c3c">RB:${stats['Rusak Berat']}</span>`;

            // ✅ TAMBAHAN: Update card statistik panjang
            updateLengthStatistics(lengthStats);

            // Auto zoom ke area dengan data
            if (allBounds.length > 0) {
                let bounds = L.latLngBounds(allBounds);
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }

        /**
        * ✅ FUNGSI BARU: Update statistik panjang di card
        */
        function updateLengthStatistics(lengthStats) {
            document.getElementById('stat-baik').innerHTML = lengthStats['Baik'].toFixed(2) + ' M';
            document.getElementById('stat-sedang').innerHTML = lengthStats['Sedang'].toFixed(2) + ' M';
            document.getElementById('stat-rusak-ringan').innerHTML = lengthStats['Rusak Ringan'].toFixed(2) + ' M';
            document.getElementById('stat-rusak-berat').innerHTML = lengthStats['Rusak Berat'].toFixed(2) + ' M';
        }

        /**
        * Fungsi untuk menambahkan legend
        */
        let legendControl = null;
        function addLegend() {
            // Remove existing legend jika ada
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