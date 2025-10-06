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
                        <div class="d-flex align-items-center">
                            <select id="yearFilter" class="form-control mr-2" style="width: 120px;">
                                <option value="">Pilih Tahun</option>
                            </select>
                            <span class="badge badge-info" id="total-links">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 80vh; width: 100%;"></div>
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
        border-radius: 5px;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
        line-height: 1.8;
    }
    
    .legend h4 {
        margin: 0 0 10px 0;
        font-size: 14px;
        font-weight: bold;
    }
    
    .legend-item {
        margin: 5px 0;
        display: flex;
        align-items: center;
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
        min-width: 200px;
    }
    
    .leaflet-popup-content strong {
        color: #333;
        font-size: 14px;
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

    // Warna berdasarkan kategori SDI
    const sdiColors = {
        'Baik': '#2ecc71',           // Hijau
        'Sedang': '#FFD93D',         // Kuning/Orange
        'Rusak Ringan': '#FF9A00',   // Orange Tua
        'Rusak Berat': '#e74c3c',    // Merah
        'Tidak Ada Data': '#95a5a6'  // Abu-abu
    };

    // Store polylines untuk clear nanti
    let polylines = [];

    // Populate year filter
    populateYearFilter();

    // Load initial data
    const currentYear = new Date().getFullYear();
    loadMapData(currentYear);

    // Event listener untuk year filter
    document.getElementById('yearFilter').addEventListener('change', function() {
        const selectedYear = this.value;
        if (selectedYear) {
            loadMapData(selectedYear);
        }
    });

    /**
     * Populate dropdown tahun
     */
    function populateYearFilter() {
        const yearFilter = document.getElementById('yearFilter');
        const currentYear = new Date().getFullYear();
        
        // Generate 5 tahun terakhir
        for (let i = 0; i < 5; i++) {
            const year = currentYear - i;
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            if (i === 0) option.selected = true;
            yearFilter.appendChild(option);
        }
    }

    /**
     * Load map data dari API
     */
    function loadMapData(year) {
        // Clear existing polylines
        polylines.forEach(p => map.removeLayer(p));
        polylines = [];

        // Update loading state
        document.getElementById('total-links').textContent = 'Loading...';

        fetch(`/api/alignment/coords-sdi?year=${year}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                console.log('Data loaded:', data);

                if (!data || data.length === 0) {
                    console.warn("Data koordinat kosong untuk tahun " + year);
                    document.getElementById('total-links').textContent = 'Tidak ada data';
                    return;
                }

                let totalSegments = 0;
                let allBounds = [];

                // Loop per segmen
                data.forEach((segment) => {
                    if (!segment.coords || segment.coords.length < 2) {
                        console.warn('Segment skipped - insufficient coordinates:', segment);
                        return;
                    }

                    totalSegments++;
                    
                    // Convert coords to Leaflet format
                    let coords = segment.coords.map(c => [c.lat, c.lng]);
                    let color = sdiColors[segment.category] || '#95a5a6';

                    // Gambar polyline per segmen
                    let polyline = L.polyline(coords, {
                        color: color,
                        weight: 5,
                        opacity: 0.8,
                        smoothFactor: 1
                    }).addTo(map);

                    polylines.push(polyline);

                    // Popup info dengan formatting lebih baik
                    polyline.bindPopup(`
                        <div style="font-family: Arial, sans-serif;">
                            <strong style="font-size: 15px;">Ruas ${segment.link_no}</strong><br>
                            <hr style="margin: 5px 0;">
                            <strong>Chainage:</strong> ${segment.chainage_from.toFixed(3)} - ${segment.chainage_to.toFixed(3)} m<br>
                            <strong>Kategori:</strong> 
                            <span style="color: ${color}; font-weight: bold; font-size: 14px;">
                                ${segment.category}
                            </span><br>
                            <strong>Nilai SDI:</strong> ${segment.sdi_final !== null ? segment.sdi_final.toFixed(2) : 'N/A'}<br>
                            <strong>Tahun:</strong> ${segment.year}
                        </div>
                    `);

                    // Add hover effect
                    polyline.on('mouseover', function() {
                        this.setStyle({ weight: 7, opacity: 1 });
                    });

                    polyline.on('mouseout', function() {
                        this.setStyle({ weight: 5, opacity: 0.8 });
                    });

                    allBounds.push(...coords);
                });

                // Hitung total ruas unik
                let uniqueLinks = [...new Set(data.map(s => s.link_no))];
                document.getElementById('total-links').textContent = 
                    `${uniqueLinks.length} Ruas | ${totalSegments} Segmen | Tahun ${year}`;

                // Auto zoom ke area dengan data
                if (allBounds.length > 0) {
                    let bounds = L.latLngBounds(allBounds);
                    map.fitBounds(bounds, { padding: [50, 50] });
                }

                // Add legend (hanya sekali)
                addLegend();
            })
            .catch(err => {
                console.error('Error loading data:', err);
                document.getElementById('total-links').textContent = 'Error: ' + err.message;
                alert('Gagal memuat data peta. Silakan refresh halaman atau hubungi administrator.');
            });
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
            
            div.innerHTML = '<h4>Kategori SDI (Surface Distress Index)</h4>';
            
            Object.keys(sdiColors).forEach(category => {
                div.innerHTML += `
                    <div class="legend-item">
                        <span class="legend-color" style="background: ${sdiColors[category]};"></span>
                        <span>${category}</span>
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