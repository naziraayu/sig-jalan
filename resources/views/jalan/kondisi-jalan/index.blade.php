@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header"> 
            <h1>Kondisi Jalan & Perhitungan SDI</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item active">Kondisi Jalan</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Analisis Kondisi Jalan</h2>
            <p class="section-lead">Menampilkan data kondisi jalan dan perhitungan Surface Distress Index (SDI).</p>

            <div class="card">
                <div class="card-header">
                    <h4>Filter Ruas Jalan</h4>
                </div>
                <div class="card-body">
                    <div class="form-row">

                        {{-- Status Ruas --}}
                        <div class="form-group col-md-3">
                            <label for="filterStatus">Pilih Status Ruas</label>
                            <select id="filterStatus" class="form-control" disabled>
                                @foreach($statusRuas as $status)
                                    <option value="{{ $status->code }}"
                                        {{ $status->code == 'K' ? 'selected' : '' }}>
                                        {{ $status->code_description_ind }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Provinsi --}}
                        <div class="form-group col-md-3">
                            <label for="filterProvinsi">Pilih Provinsi</label>
                            <select id="filterProvinsi" class="form-control" disabled>
                                @foreach($provinsi as $prov)
                                    <option value="{{ $prov->province_code }}"
                                        {{ $prov->province_name == 'Jawa Timur' ? 'selected' : '' }}>
                                        {{ $prov->province_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kabupaten --}}
                        <div class="form-group col-md-3">
                            <label for="filterKabupaten">Pilih Kabupaten</label>
                            <select id="filterKabupaten" class="form-control">
                                @foreach($kabupaten as $kab)
                                    <option value="{{ $kab->kabupaten_code }}"
                                        {{ $kab->kabupaten_name == 'Jember' ? 'selected' : '' }}>
                                        {{ $kab->kabupaten_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tahun (PRIORITAS PERTAMA) --}}
                        <div class="form-group col-md-3">
                            <label for="filterYear">
                                Tahun Data <span class="text-danger">*</span>
                            </label>
                            <select id="filterYear" class="form-control">
                                <option value="">-- Pilih Tahun --</option>
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ruas (PRIORITAS KEDUA) --}}
                        <div class="form-group col-md-3">
                            <label for="filterRuas">
                                Pilih Ruas <span class="text-danger">*</span>
                            </label>
                            <select id="filterRuas" class="form-control" disabled>
                                <option value="">-- Pilih Tahun Terlebih Dahulu --</option>
                            </select>
                        </div>

                        {{-- Tombol Filter --}}
                        <div class="form-group col-md-3 d-flex align-items-end">
                            <button type="button" id="btnFilter" class="btn btn-primary btn-block" disabled>
                                <i class="fas fa-filter"></i> Tampilkan Data
                            </button>
                        </div>

                        {{-- Tombol Reset --}}
                        <div class="form-group col-md-3 d-flex align-items-end">
                            <button type="button" id="btnReset" class="btn btn-secondary btn-block">
                                <i class="fas fa-redo"></i> Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info SDI --}}
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="mb-3"><i class="fas fa-info-circle"></i> Kategori Surface Distress Index (SDI)</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="badge badge-success badge-lg p-2 w-100">
                                <i class="fas fa-check-circle"></i> Baik (SDI < 50)
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="badge badge-warning badge-lg p-2 w-100" style="background-color: #FFD700; color: #fff;">
                                <i class="fas fa-exclamation-circle"></i> Sedang (50-100)
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="badge badge-danger badge-lg p-2 w-100" style="background-color: #FFA500; color: #fff;">
                                <i class="fas fa-times-circle"></i> Rusak Ringan (100-150)
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="badge badge-danger badge-lg p-2 w-100">
                                <i class="fas fa-ban"></i> Rusak Berat (SDI ≥ 150)
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel hasil pilihan --}}
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Data Kondisi Jalan & Perhitungan SDI</h4>
                    <div class="d-flex flex-wrap gap-2">

                        {{-- Hapus Semua --}}
                        @if(auth()->user()->hasPermission('delete','kondisi_jalan'))
                            <form action="{{ route('kondisi-jalan.destroyAll') }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus semua data kondisi jalan? Semua data akan hilang!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon icon-left btn-danger">
                                    <i class="fas fa-trash"></i> Hapus Semua
                                </button>
                            </form>
                        @endif

                        {{-- Import / Export --}}
                        @if(auth()->user()->hasPermission('import','kondisi_jalan') || auth()->user()->hasPermission('export','kondisi_jalan'))
                            <button type="button" class="btn btn-icon icon-left btn-success" data-toggle="modal" data-target="#modalImportExport">
                                <i class="fas fa-file-excel"></i> Import / Export
                            </button>
                        @endif

                        {{-- Tambah Data --}}
                        @if(auth()->user()->hasPermission('add','kondisi_jalan'))
                            <a href="{{ route('kondisi-jalan.create') }}" class="btn btn-icon icon-left btn-primary">
                                <i class="fas fa-plus"></i> Tambah Data
                            </a>
                        @endif

                    </div>
                </div>

                <div class="card-body">
                    <div id="detailRuas">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Petunjuk:</strong> Silakan pilih <strong>Tahun</strong> terlebih dahulu, kemudian pilih <strong>Ruas Jalan</strong> yang tersedia pada tahun tersebut, lalu klik tombol <strong>"Tampilkan Data"</strong> untuk melihat data kondisi jalan dan perhitungan SDI.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

{{-- Modal Import Export --}}
@include('components.modals.import_export', [
    'title' => 'Import / Export Kondisi Jalan',
    'importRoute' => route('kondisi-jalan.import'),
    'exportRoute' => route('kondisi-jalan.export'),
]) 

@endsection

@push('scripts')
<script>
    $(document).ready(function(){
        
        // ALUR BARU: Ketika TAHUN dipilih (PRIORITAS PERTAMA)
        $('#filterYear').on('change', function(){
            let year = $(this).val();
            
            if(year){
                // Reset ruas
                $('#filterRuas').html('<option value="">-- Memuat ruas... --</option>').prop('disabled', true);
                $('#btnFilter').prop('disabled', true);
                
                // Muat ruas yang tersedia untuk tahun ini
                $.ajax({
                    url: "{{ route('kondisi-jalan.getRuasByYear') }}",
                    type: "GET",
                    data: { year: year },
                    success: function(res){
                        if(res.success && res.data.length > 0){
                            let options = '<option value="">-- Pilih Ruas --</option>';
                            res.data.forEach(function(ruas){
                                options += `<option value="${ruas.link_no}">${ruas.link_code} - ${ruas.link_name}</option>`;
                            });
                            $('#filterRuas').html(options).prop('disabled', false);
                        } else {
                            $('#filterRuas').html('<option value="">-- Tidak ada data ruas --</option>');
                            Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan',
                                text: 'Tidak ada data ruas untuk tahun ini',
                            });
                        }
                    },
                    error: function(){
                        $('#filterRuas').html('<option value="">-- Gagal memuat ruas --</option>');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat memuat data ruas',
                        });
                    }
                });
            } else {
                $('#filterRuas').html('<option value="">-- Pilih Tahun Terlebih Dahulu --</option>').prop('disabled', true);
                $('#btnFilter').prop('disabled', true);
                $('#detailRuas').html(`
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Petunjuk:</strong> Silakan pilih <strong>Tahun</strong> terlebih dahulu, kemudian pilih <strong>Ruas Jalan</strong> yang tersedia pada tahun tersebut, lalu klik tombol <strong>"Tampilkan Data"</strong> untuk melihat data kondisi jalan dan perhitungan SDI.
                    </div>
                `);
            }
        });

        // Ketika RUAS dipilih (PRIORITAS KEDUA)
        $('#filterRuas').on('change', function(){
            let linkNo = $(this).val();
            let year = $('#filterYear').val();
            
            if(linkNo && year){
                $('#btnFilter').prop('disabled', false);
            } else {
                $('#btnFilter').prop('disabled', true);
            }
        });

        // Tombol Filter
        $('#btnFilter').on('click', function(){
            let linkNo = $('#filterRuas').val();
            let year = $('#filterYear').val();
            
            if(linkNo && year){
                loadRoadConditionData(linkNo, year);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih tahun dan ruas terlebih dahulu!',
                });
            }
        });

        // Tombol Reset
        $('#btnReset').on('click', function(){
            $('#filterYear').val('').trigger('change');
            $('#filterRuas').html('<option value="">-- Pilih Tahun Terlebih Dahulu --</option>').prop('disabled', true);
            $('#btnFilter').prop('disabled', true);
            
            // Destroy DataTable jika ada
            if ($.fn.DataTable.isDataTable('#detailRuasTable')) {
                $('#detailRuasTable').DataTable().destroy();
            }
            
            $('#detailRuas').html(`
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Petunjuk:</strong> Silakan pilih <strong>Tahun</strong> terlebih dahulu, kemudian pilih <strong>Ruas Jalan</strong> yang tersedia pada tahun tersebut, lalu klik tombol <strong>"Tampilkan Data"</strong> untuk melihat data kondisi jalan dan perhitungan SDI.
                </div>
            `);
        });

        function loadRoadConditionData(linkNo, year){
            // Show loading
            $('#detailRuas').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3">Memuat data kondisi jalan...</p>
                </div>
            `);

            $.ajax({
                url: "{{ route('kondisi-jalan.getDetail') }}",
                type: "GET",
                data: {
                    link_no: linkNo,
                    year: year
                },
                success: function(res){
                    if(res.success){
                        let html = `
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> 
                                Data berhasil dimuat: <strong>${res.data.length} segmen</strong> untuk ruas <strong>${linkNo}</strong> tahun <strong>${year}</strong>
                            </div>
                            <div class="table-responsive">
                                <table id="detailRuasTable" class="table table-striped table-bordered table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th rowspan="2" class="align-middle text-center">No</th>
                                            <th rowspan="2" class="align-middle">Chainage</th>
                                            <th rowspan="2" class="align-middle text-center">Tahun</th>
                                            <th rowspan="2" class="align-middle text-center">Lebar Jalan (m)</th>
                                            <th colspan="4" class="text-center bg-info text-white">Perhitungan SDI</th>
                                            <th rowspan="2" class="align-middle text-center">Kategori</th>
                                            <th rowspan="2" class="align-middle text-center">Detail</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center bg-light">SDI1<br><small>(Luas Retak)</small></th>
                                            <th class="text-center bg-light">SDI2<br><small>(Lebar Retak)</small></th>
                                            <th class="text-center bg-light">SDI3<br><small>(Lubang)</small></th>
                                            <th class="text-center bg-light">SDI4<br><small>(Final)</small></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        res.data.forEach(function(item, index){
                            // Tentukan warna badge berdasarkan kategori
                            let badgeClass = 'badge';
                            let badgeStyle = '';
                            let iconClass = 'fa-question';
                            
                            if(item.sdi_category === 'Baik') {
                                badgeClass += ' badge-success';
                                iconClass = 'fa-check-circle';
                            } 
                            else if(item.sdi_category === 'Sedang') {
                                badgeClass += ' badge-warning';
                                badgeStyle = 'background-color: #FFD700; color: #fff;';
                                iconClass = 'fa-exclamation-circle';
                            } 
                            else if(item.sdi_category === 'Rusak Ringan') {
                                badgeClass += ' badge-warning';
                                badgeStyle = 'background-color: #FF9A00; color: #fff;';
                                iconClass = 'fa-times-circle';
                            } 
                            else if(item.sdi_category === 'Rusak Berat') {
                                badgeClass += ' badge-danger';
                                iconClass = 'fa-ban';
                            }

                            html += `
                                <tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td><strong>${item.chainage_from}</strong> - <strong>${item.chainage_to}</strong></td>
                                    <td class="text-center">${item.year}</td>
                                    <td class="text-center">${item.pave_width.toFixed(2)}</td>
                                    <td class="text-center">${item.sdi1.toFixed(2)}</td>
                                    <td class="text-center">${item.sdi2.toFixed(2)}</td>
                                    <td class="text-center">${item.sdi3.toFixed(2)}</td>
                                    <td class="text-center font-weight-bold text-primary">${item.sdi_final.toFixed(2)}</td>
                                    <td class="text-center">
                                        <span class="${badgeClass}" style="${badgeStyle}">
                                            <i class="fas ${iconClass}"></i> ${item.sdi_category}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="/kondisi-jalan/show/${item.link_no}/${item.year}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Lihat Ruas
                                        </a>
                                    </td>
                                </tr>
                            `;
                        });


                        html += `
                                    </tbody>
                                </table>
                            </div>
                        `;

                        $('#detailRuas').html(html);

                        // Inisialisasi DataTables
                        $('#detailRuasTable').DataTable({
                            responsive: true,
                            autoWidth: false,
                            pageLength: 25,
                            lengthMenu: [10, 25, 50, 100, 200],
                            order: [], // JANGAN SORT, data sudah urut dari backend
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                            },
                            dom: 'Bfrtip',
                            buttons: [
                                {
                                    extend: 'excel',
                                    text: '<i class="fas fa-file-excel"></i> Export Excel',
                                    className: 'btn btn-success btn-sm',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                                    }
                                },
                                {
                                    extend: 'pdf',
                                    text: '<i class="fas fa-file-pdf"></i> Export PDF',
                                    className: 'btn btn-danger btn-sm',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                                    },
                                    orientation: 'landscape',
                                    pageSize: 'A4'
                                },
                                {
                                    extend: 'print',
                                    text: '<i class="fas fa-print"></i> Print',
                                    className: 'btn btn-info btn-sm',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                                    }
                                }
                            ]
                        });

                    } else {
                        $('#detailRuas').html(`
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> 
                                ${res.message || 'Data tidak ditemukan untuk ruas dan tahun yang dipilih'}
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    $('#detailRuas').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i> 
                            Terjadi kesalahan saat memuat data. Silakan coba lagi.
                        </div>
                    `);
                }
            });
        }
    });

    // Fungsi untuk menampilkan detail SDI LENGKAP
    function showSDIDetail(data) {
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
                link_no: data.link_no,
                chainage_from: data.chainage_from,
                chainage_to: data.chainage_to,
                year: data.year
            },
            success: function(res) {
                if (res.success) {
                    const sdiData = res.data.sdi_detail;
                    const condition = res.data.condition;
                    
                    // Tentukan badge category
                    const category = sdiData.final.category;
                    let badgeClass = 'badge-secondary';
                    
                    if (category === 'Baik') badgeClass = 'badge-success';
                    else if (category === 'Sedang') badgeClass = 'badge-warning';
                    else if (category === 'Rusak Ringan') badgeClass = 'badge-danger';
                    else if (category === 'Rusak Berat') badgeClass = 'badge-dark';

                    Swal.fire({
                        title: `Detail Perhitungan SDI`,
                        html: `
                            <div class="text-left" style="max-height: 600px; overflow-y: auto;">
                                <div class="alert alert-info mb-3">
                                    <h5 class="text-center mb-2">
                                        <i class="fas fa-road"></i> 
                                        <strong>${condition.link_no.link_code} - ${condition.link_no.link_name}</strong>
                                    </h5>
                                    <p class="mb-0 text-center">
                                        Chainage: <strong>${data.chainage_from} - ${data.chainage_to}</strong> | 
                                        Tahun: <strong>${data.year}</strong>
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
                                        <td><strong>${sdiData.raw_data.pave_width.toFixed(2)} m</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Panjang Segmen</strong></td>
                                        <td>${sdiData.raw_data.segment_length.toFixed(3)} km</td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td><strong>Luas Total Segmen</strong></td>
                                        <td><strong>${sdiData.raw_data.total_segment_area.toFixed(2)} m²</strong></td>
                                    </tr>
                                </table>

                                <!-- TAHAP 1: LUAS RETAK -->
                                <div class="card mb-3 border-primary">
                                    <div class="card-header bg-primary text-white py-2">
                                        <strong><i class="fas fa-layer-group"></i> TAHAP 1: Perhitungan Luas Retak (SDI1)</strong>
                                    </div>
                                    <div class="card-body p-2">
                                        <p class="mb-2"><small><em>${sdiData.calculations.step1.formula}</em></small></p>
                                        
                                        <table class="table table-sm table-bordered mb-2">
                                            <tr>
                                                <td>Crack Depression Area</td>
                                                <td class="text-right">${sdiData.raw_data.crack_dep_area.toFixed(2)} m²</td>
                                            </tr>
                                            <tr>
                                                <td>Other Crack Area</td>
                                                <td class="text-right">${sdiData.raw_data.oth_crack_area.toFixed(2)} m²</td>
                                            </tr>
                                            <tr>
                                                <td>Concrete Cracking Area</td>
                                                <td class="text-right">${sdiData.raw_data.concrete_cracking_area.toFixed(2)} m²</td>
                                            </tr>
                                            <tr>
                                                <td>Concrete Structural Area</td>
                                                <td class="text-right">${sdiData.raw_data.concrete_structural_area.toFixed(2)} m²</td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td><strong>Total Luas Retak</strong></td>
                                                <td class="text-right"><strong>${sdiData.raw_data.total_crack_area.toFixed(2)} m²</strong></td>
                                            </tr>
                                            <tr class="table-info">
                                                <td><strong>% Luas Retak</strong></td>
                                                <td class="text-right"><strong>${sdiData.raw_data.crack_percentage.toFixed(2)}%</strong></td>
                                            </tr>
                                        </table>
                                        
                                        <div class="alert alert-success mb-0 py-2">
                                            <strong>Hasil:</strong> ${sdiData.calculations.step1.explanation}
                                            <div class="text-right mt-1"><strong>SDI1 = ${sdiData.calculations.step1.value.toFixed(2)}</strong></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAHAP 2: LEBAR RETAK -->
                                <div class="card mb-3 border-info">
                                    <div class="card-header bg-info text-white py-2">
                                        <strong><i class="fas fa-arrows-alt-h"></i> TAHAP 2: Perhitungan Lebar Retak (SDI2)</strong>
                                    </div>
                                    <div class="card-body p-2">
                                        <p class="mb-2"><small><em>${sdiData.calculations.step2.formula}</em></small></p>
                                        
                                        <table class="table table-sm table-bordered mb-2">
                                            <tr>
                                                <td><strong>Lebar Retak (Crack Width)</strong></td>
                                                <td class="text-right"><strong>${sdiData.raw_data.crack_width.toFixed(2)} mm</strong></td>
                                            </tr>
                                        </table>
                                        
                                        <div class="alert alert-success mb-0 py-2">
                                            <strong>Hasil:</strong> ${sdiData.calculations.step2.explanation}
                                            <div class="text-right mt-1"><strong>SDI2 = ${sdiData.calculations.step2.value.toFixed(2)}</strong></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAHAP 3: JUMLAH LUBANG -->
                                <div class="card mb-3 border-warning">
                                    <div class="card-header bg-warning text-dark py-2">
                                        <strong><i class="fas fa-circle-notch"></i> TAHAP 3: Perhitungan Jumlah Lubang (SDI3)</strong>
                                    </div>
                                    <div class="card-body p-2">
                                        <p class="mb-2"><small><em>${sdiData.calculations.step3.formula}</em></small></p>
                                        
                                        <table class="table table-sm table-bordered mb-2">
                                            <tr>
                                                <td><strong>Jumlah Lubang (Pothole)</strong></td>
                                                <td class="text-right"><strong>${sdiData.raw_data.pothole_count}</strong></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Normalisasi per 100m</strong></td>
                                                <td class="text-right"><strong>${sdiData.raw_data.normalized_potholes.toFixed(2)}</strong></td>
                                            </tr>
                                            <tr class="table-info">
                                                <td><strong>Penambahan Nilai</strong></td>
                                                <td class="text-right"><strong>+ ${sdiData.calculations.step3.addition.toFixed(2)}</strong></td>
                                            </tr>
                                        </table>
                                        
                                        <div class="alert alert-success mb-0 py-2">
                                            <strong>Hasil:</strong> ${sdiData.calculations.step3.explanation}
                                            <div class="text-right mt-1"><strong>SDI3 = ${sdiData.calculations.step3.value.toFixed(2)}</strong></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAHAP 4: KEDALAMAN ALUR -->
                                <div class="card mb-3 border-danger">
                                    <div class="card-header bg-danger text-white py-2">
                                        <strong><i class="fas fa-water"></i> TAHAP 4: Perhitungan Kedalaman Alur Roda (SDI4 - Final)</strong>
                                    </div>
                                    <div class="card-body p-2">
                                        <p class="mb-2"><small><em>${sdiData.calculations.step4.formula}</em></small></p>
                                        
                                        <table class="table table-sm table-bordered mb-2">
                                            <tr>
                                                <td><strong>Kedalaman Alur (Rutting Depth)</strong></td>
                                                <td class="text-right"><strong>${sdiData.raw_data.rutting_depth.toFixed(2)} cm</strong></td>
                                            </tr>
                                            <tr class="table-info">
                                                <td><strong>Penambahan Nilai</strong></td>
                                                <td class="text-right"><strong>+ ${sdiData.calculations.step4.addition.toFixed(2)}</strong></td>
                                            </tr>
                                        </table>
                                        
                                        <div class="alert alert-success mb-0 py-2">
                                            <strong>Hasil:</strong> ${sdiData.calculations.step4.explanation}
                                            <div class="text-right mt-1"><strong>SDI4 = ${sdiData.calculations.step4.value.toFixed(2)}</strong></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- HASIL AKHIR -->
                                <div class="card border-dark">
                                    <div class="card-header bg-dark text-white text-center py-2">
                                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> HASIL AKHIR PERHITUNGAN SDI</h5>
                                    </div>
                                    <div class="card-body text-center p-3">
                                        <h2 class="text-primary mb-2">${sdiData.final.sdi_final.toFixed(2)}</h2>
                                        <span class="badge ${badgeClass}" style="font-size: 18px; padding: 10px 20px;">
                                            <i class="fas fa-flag"></i> ${sdiData.final.category}
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
                                        <strong>Catatan:</strong> Perhitungan SDI mengikuti Panduan Bina Marga untuk evaluasi kondisi permukaan jalan.
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
</script>

<style>
.sdi-detail-modal .table {
    font-size: 14px;
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