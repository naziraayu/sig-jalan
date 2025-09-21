@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Ruas Jalan Kecamatan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item active">Ruas Jalan Kecamatan</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Daftar Ruas Jalan Kecamatan</h2>
            <p class="section-lead">Menampilkan data ruas jalan kecamatan berdasarkan pilihan dropdown.</p>

            <div class="card">
                <div class="card-header">
                    <h4>Filter Ruas Jalan Kecamatan</h4>
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
                                @foreach($provinces as $prov)
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
                                @foreach($kabupatenList as $kab)
                                    <option value="{{ $kab->kabupaten_code }}"
                                        {{ $kab->kabupaten_name == 'Jember' ? 'selected' : '' }}>
                                        {{ $kab->kabupaten_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ruas --}}
                        <div class="form-group col-md-3">
                            <label for="filterRuas">Pilih Ruas</label>
                            <select id="filterRuas" class="form-control">
                                <option value="">-- Pilih Ruas --</option>
                                @foreach($ruasList as $ruas)
                                    <option value="{{ $ruas->link_no }}">
                                        {{ $ruas->link_code }} - {{ $ruas->link_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel hasil pilihan --}}
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Data Ruas Jalan Kecamatan</h4>
                    <div class="d-flex flex-wrap gap-2">

                        {{-- Hapus Semua --}}
                        @if(auth()->user()->hasPermission('delete','ruas_jalan_kecamatan'))
                            <form action="{{ route('ruas-jalan-kecamatan.destroyAll') }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus semua data ruas jalan kecamatan? Semua data akan hilang!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon icon-left btn-danger">
                                    <i class="fas fa-trash"></i> Hapus Semua
                                </button>
                            </form>
                        @endif

                        {{-- Import / Export --}}
                        @if(auth()->user()->hasPermission('import','ruas_jalan_kecamatan') || auth()->user()->hasPermission('export','ruas_jalan_kecamatan'))
                            <button type="button" class="btn btn-icon icon-left btn-success" data-toggle="modal" data-target="#modalImportExport">
                                <i class="fas fa-file-excel"></i> Import / Export
                            </button>
                        @endif

                        {{-- Tambah Data --}}
                        @if(auth()->user()->hasPermission('add','ruas_jalan_kecamatan'))
                            <a href="{{ route('ruas-jalan-kecamatan.create') }}" class="btn btn-icon icon-left btn-primary">
                                <i class="fas fa-plus"></i> Tambah Data
                            </a>
                        @endif

                    </div>
                </div>

                <div class="card-body">
                    <div id="detailRuas">
                        <p class="text-muted">Silakan pilih ruas untuk menampilkan data.</p>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

{{-- Modal Import Export --}}
@include('components.modals.import_export', [
    'title' => 'Import / Export Ruas Jalan Kecamatan',
    'importRoute' => route('ruas-jalan-kecamatan.import'),
    'exportRoute' => route('ruas-jalan-kecamatan.export'),
]) 

@endsection

@push('scripts')
<script>
$(document).ready(function(){
    $('#filterRuas').on('change', function(){
        let linkNo = $(this).val();
        console.log('Selected link_no:', linkNo);
        
        if(linkNo){
            $.ajax({
                url: "{{ route('ruas-jalan-kecamatan.getDetail') }}",
                type: "GET",
                data: {link_no: linkNo},
                beforeSend: function() {
                    $('#detailRuas').html('<p class="text-info"><i class="fas fa-spinner fa-spin"></i> Loading...</p>');
                },
                success: function(res){
                    console.log('Ajax success - Full response:', res);
                    
                    if(res.success && res.data && res.data.length > 0){
                        let html = `
                            <table id="detailRuasTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Provinsi</th>
                                        <th>Kabupaten</th>
                                        <th>Nama Ruas</th>
                                        <th>Kecamatan</th>
                                        <th>DRP From</th>
                                        <th>DRP To</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        res.data.forEach(function(item, index){
                            console.log(`Processing item ${index}:`, item);
                            
                            let provinceName = (item.province && item.province.province_name) ? item.province.province_name : 'N/A';
                            let kabupatenName = (item.kabupaten && item.kabupaten.kabupaten_name) ? item.kabupaten.kabupaten_name : 'N/A';
                            let linkName = (item.linkNo && item.linkNo.link_name) ? item.linkNo.link_name : 'N/A';
                            let linkCode = (item.linkNo && item.linkNo.link_code) ? item.linkNo.link_code : 'N/A';
                            let kecamatanName = (item.kecamatan && item.kecamatan.kecamatan_name) ? item.kecamatan.kecamatan_name : 'N/A';
                            
                            // Handle DRP data dengan prioritas drp_name dari relasi
                            let drpFromName = 'N/A';
                            if (item.drpFrom && item.drpFrom.drp_name) {
                                drpFromName = item.drpFrom.drp_name;
                            } else if (item.drp_from) {
                                drpFromName = item.drp_from; // Fallback ke raw value
                            }
                            
                            let drpToName = 'N/A';
                            if (item.drpTo && item.drpTo.drp_name) {
                                drpToName = item.drpTo.drp_name;
                            } else if (item.drp_to) {
                                drpToName = item.drp_to; // Fallback ke raw value
                            }
                            
                            html += `
                                <tr>
                                    <td>${provinceName}</td>
                                    <td>${kabupatenName}</td>
                                    <td>${linkCode} - ${linkName}</td>
                                    <td>${kecamatanName}</td>
                                    <td>${drpFromName}</td>
                                    <td>${drpToName}</td>
                                    <td>
                                        <a href="/pengaturan-jaringan/ruas-jalan-kecamatan/${item.link_no}" class="btn btn-sm btn-info" title="Lihat Detail">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            `;
                        });

                        html += `</tbody></table>`;
                        $('#detailRuas').html(html);

                        // Initialize DataTables
                        try {
                            $('#detailRuasTable').DataTable({
                                responsive: true,
                                autoWidth: false,
                                pageLength: 10,
                                lengthMenu: [5, 10, 25, 50, 100],
                                destroy: true,
                                language: {
                                    "lengthMenu": "Tampilkan _MENU_ entri",
                                    "zeroRecords": "Tidak ada data yang ditemukan",
                                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                                    "infoFiltered": "(difilter dari _MAX_ total entri)",
                                    "search": "Cari:",
                                    "paginate": {
                                        "first": "Pertama",
                                        "last": "Terakhir", 
                                        "next": "Selanjutnya",
                                        "previous": "Sebelumnya"
                                    }
                                }
                            });
                        } catch(dtError) {
                            console.error('DataTable initialization error:', dtError);
                        }
                    } else {
                        let message = res.message || 'Data tidak ditemukan untuk ruas yang dipilih';
                        $('#detailRuas').html(`<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> ${message}</div>`);
                        
                        if (res.debug) {
                            console.log('Debug info:', res.debug);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('=== Ajax Error Details ===');
                    console.error('Status:', xhr.status);
                    console.error('Status Text:', xhr.statusText);
                    console.error('Response Text:', xhr.responseText);
                    console.error('Error:', error);
                    
                    let errorMsg = 'Terjadi kesalahan saat memuat data';
                    let errorDetails = '';
                    
                    try {
                        let errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.message) {
                            errorDetails = '<br><small>' + errorResponse.message + '</small>';
                        }
                    } catch(e) {
                        // Ignore parsing error
                    }
                    
                    if (xhr.status === 404) {
                        errorMsg = 'Endpoint tidak ditemukan (404)';
                    } else if (xhr.status === 500) {
                        errorMsg = 'Kesalahan server internal (500)';
                    } else if (xhr.status === 0) {
                        errorMsg = 'Tidak dapat terhubung ke server';
                    }
                    
                    $('#detailRuas').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> ${errorMsg}${errorDetails}
                            <br><small>Periksa console browser untuk detail error.</small>
                        </div>
                    `);
                }
            });
        } else {
            $('#detailRuas').html('<p class="text-muted"><i class="fas fa-info-circle"></i> Silakan pilih ruas untuk menampilkan data.</p>');
        }
    });

    $('#filterKabupaten').on('change', function() {
        var kabupatenCode = $(this).val();
        console.log('Kabupaten changed:', kabupatenCode);
        
        if (kabupatenCode) {
            $.ajax({
                url: '/api/ruas/' + kabupatenCode,
                method: 'GET',
                beforeSend: function() {
                    $('#filterRuas').html('<option value="">Loading...</option>');
                },
                success: function(data) {
                    console.log('Ruas data received:', data);
                    $('#filterRuas').empty().append('<option value="">-- Pilih Ruas --</option>');
                    $.each(data, function(index, ruas) {
                        $('#filterRuas').append('<option value="' + ruas.link_no + '">' + ruas.link_code + ' - ' + ruas.link_name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading ruas:', error);
                    $('#filterRuas').html('<option value="">Error loading data</option>');
                }
            });
        } else {
            $('#filterRuas').empty().append('<option value="">-- Pilih Ruas --</option>');
        }
        
        $('#detailRuas').html('<p class="text-muted"><i class="fas fa-info-circle"></i> Silakan pilih ruas untuk menampilkan data.</p>');
    });
});
</script>
@endpush