@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Data DRP (Distance Reference Point)</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item active">DRP</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Daftar DRP</h2>
            <p class="section-lead">Menampilkan data DRP berdasarkan pilihan dropdown.</p>

            <div class="card">
                <div class="card-header">
                    <h4>Filter DRP</h4>
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

                        {{-- Ruas --}}
                        <div class="form-group col-md-3">
                            <label for="filterRuas">Pilih Ruas</label>
                            <select id="filterRuas" class="form-control">
                                <option value="">-- Pilih Ruas --</option>
                                @foreach($ruasjalan as $ruas)
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
                    <h4>Data DRP</h4>
                    <div class="d-flex flex-wrap gap-2">

                        {{-- Hapus Semua --}}
                        @if(auth()->user()->hasPermission('delete','drp'))
                            <form action="{{ route('drp.destroyAll') }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus semua data DRP? Semua data akan hilang!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon icon-left btn-danger">
                                    <i class="fas fa-trash"></i> Hapus Semua
                                </button>
                            </form>
                        @endif

                        {{-- Import / Export --}}
                        @if(auth()->user()->hasPermission('import','drp') || auth()->user()->hasPermission('export','drp'))
                            <button type="button" class="btn btn-icon icon-left btn-success" data-toggle="modal" data-target="#modalImportExport">
                                <i class="fas fa-file-excel"></i> Import / Export
                            </button>
                        @endif

                        {{-- Tambah Data --}}
                        @if(auth()->user()->hasPermission('add','drp'))
                            <a href="{{ route('drp.create') }}" class="btn btn-icon icon-left btn-primary">
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
    'title' => 'Import / Export DRP',
    'importRoute' => route('drp.import'),
    'exportRoute' => route('drp.export'),
]) 

@endsection

@push('scripts')
<script>
$(document).ready(function(){
    $('#filterRuas').on('change', function(){
        let linkNo = $(this).val();
        if(linkNo){
            $.ajax({
                url: "{{ route('drp.getDetail') }}",
                type: "GET",
                data: {link_no: linkNo},
                success: function(res){
                    if(res.success){
                        // Buat struktur tabel datatables
                        let html = `
                            <table id="detailRuasTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nomor DRP</th>
                                        <th>KM</th>
                                        <th>Panjang DRP</th>
                                        <th>Tipe DRP</th>
                                        <th>Deskripsi DRP (0+100)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        res.data.forEach(function(item){
                            console.log('Full item:', item);
                            
                            // Format koordinat
                            let koordinat = '';
                            if (item.dpr_north_deg && item.dpr_east_deg) {
                                koordinat = `${item.dpr_north_deg}°${item.dpr_north_min || 0}'${item.dpr_north_sec || 0}" N, ${item.dpr_east_deg}°${item.dpr_east_min || 0}'${item.dpr_east_sec || 0}" E`;
                            }
                            
                            html += `
                                <tr>
                                    <td>${item.drp_num || '-'}</td>
                                    <td>${item.chainage || '-'}</td>
                                    <td>${item.drp_length || '-'}</td>
                                    <td>${item.type?.code_description_eng ?? '-'}</td>
                                    <td>${item.drp_desc || '-'}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if(auth()->user()->hasPermission('update','drp'))
                                                <a href="/drp/${item.drp_num}/edit" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('delete','drp'))
                                                <form action="/drp/${item.drp_num}" method="POST" class="d-inline" 
                                                    onsubmit="return confirm('Yakin ingin menghapus data DRP ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });

                        html += `</tbody></table>`;

                        $('#detailRuas').html(html);

                        // Inisialisasi DataTables setelah tabel ditambahkan
                        $('#detailRuasTable').DataTable({
                            responsive: true,
                            autoWidth: false,
                            pageLength: 10,
                            lengthMenu: [5, 10, 25, 50, 100],
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                            }
                        });
                    } else {
                        $('#detailRuas').html('<p class="text-danger">Data tidak ditemukan</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    $('#detailRuas').html('<p class="text-danger">Terjadi kesalahan saat memuat data</p>');
                }
            });
        } else {
            $('#detailRuas').html('<p class="text-muted">Silakan pilih ruas untuk menampilkan data.</p>');
        }
    });
    // Di bagian AJAX untuk perubahan kabupaten, pastikan route sesuai
$('#filterKabupaten').on('change', function(){
    let kabupatenCode = $(this).val();
    
    if(kabupatenCode) {
        // Route sesuai dengan yang ada: /drp/get-links
        getRuasJalan(kabupatenCode);
    }
    
    // Reset data
    $('#filterRuas').val('');
    $('#detailRuas').html('<p class="text-muted">Silakan pilih ruas untuk menampilkan data.</p>');
});

function getRuasJalan(kabupatenCode) {
    $.ajax({
        url: "{{ route('drp.getLinks') }}", // Sesuai dengan route yang ada
        type: "GET", 
        data: {kabupaten_code: kabupatenCode},
        success: function(res){
            // Handle response sama seperti sebelumnya
        }
    });
}
});
</script>
@endpush