@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>DRP Management</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item active">DRP</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Daftar DRP</h2>
            <p class="section-lead">Menampilkan seluruh data DRP berdasarkan ruas jalan.</p>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h4 class="mb-2 mb-md-0">Tabel DRP</h4>

                    <div class="d-flex justify-content-between align-items-center flex-wrap w-100 mt-2 mt-md-0">
                        {{-- Dropdown filter kiri --}}
                        <div class="d-flex gap-2">
                            {{-- Status (default Kabupaten, disabled) --}}
                            <select id="filterStatus" class="custom-select" style="border-radius: 0; min-width: 150px;" disabled>
                                @foreach($status as $st)
                                    <option value="{{ $st->code }}" {{ $st->code == $defaultStatus ? 'selected' : '' }}>
                                        {{ $st->description }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Provinsi (default Jawa Timur, disabled) --}}
                            <select id="filterProvinsi" class="custom-select" style="border-radius: 0; min-width: 150px;" disabled>
                                @foreach($provinsi as $prov)
                                    <option value="{{ $prov->province_code }}" {{ $prov->province_code == $defaultProvinsi ? 'selected' : '' }}>
                                        {{ $prov->province_name }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Kabupaten (default Jember, aktif) --}}
                            <select id="filterKabupaten" class="custom-select" style="border-radius: 0; min-width: 150px;">
                                @foreach($kabupaten as $kab)
                                    <option value="{{ $kab->kabupaten_code }}" {{ $kab->kabupaten_code == $defaultKabupaten ? 'selected' : '' }}>
                                        {{ $kab->kabupaten_name }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Ruas (bebas dipilih) --}}
                            <select id="filterRuas" class="custom-select" style="border-radius: 0; min-width: 150px;">
                                <option value="">-- Pilih Ruas --</option>
                                @foreach($link as $lnk)
                                    <option value="{{ $lnk->link_no }}" {{ $lnk->link_no == $defaultLink ? 'selected' : '' }}>
                                        {{ $lnk->link_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tombol aksi kanan --}}
                        <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                            @if(auth()->user()->hasPermission('delete','drp'))
                                <form action="{{ route('drp.destroyAll') }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus semua data DRP? Semua data akan hilang!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Hapus Semua Data
                                    </button>
                                </form>
                            @endif

                            @if(auth()->user()->hasPermission('import','drp') || auth()->user()->hasPermission('export','drp'))
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalImportExport">
                                    <i class="fas fa-exchange-alt"></i> Import / Export
                                </button>
                            @endif

                            @if(auth()->user()->hasPermission('add','drp'))
                                <a href="{{ route('drp.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tambah DRP
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Alert sukses --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    {{-- Alert error --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    {{-- Loading indicator --}}
                    <div id="loading" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data...</p>
                    </div>

                    <div class="table-responsive">
                        <table id="drpTable" class="table table-striped table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nomor DRP</th>
                                    <th>KM</th>
                                    <th>Panjang DRP</th>
                                    <th>Tipe DRP</th>
                                    <th>Deskripsi DRP (0+000)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data akan dimuat via AJAX --}}
                            </tbody>
                        </table>
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
    // Inisialisasi DataTable dengan server-side processing
    let table = $('#drpTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('drp.index') }}",
            data: function (d) {
                d.status_filter    = $('#filterStatus').val();
                d.provinsi_filter  = $('#filterProvinsi').val();
                d.kabupaten_filter = $('#filterKabupaten').val();
                d.ruas_filter      = $('#filterRuas').val();
            }
        },
        columns: [
            { data: 'drp_num', name: 'drp_num' },
            { data: 'chainage', name: 'chainage' },
            { data: 'drp_length', name: 'drp_length' },
            { data: 'type_description', name: 'type_description', orderable: false, searchable: true },
            { data: 'drp_desc', name: 'drp_desc' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'asc']],
        language: {
            processing: "Sedang memproses...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir", 
                next: "Selanjutnya",
                previous: "Sebelumnya"
            },
            emptyTable: "Tidak ada data yang tersedia",
            zeroRecords: "Tidak ada data yang cocok dengan pencarian"
        }
    });

    // Event listener untuk filter dropdown
    $('#filterStatus, #filterProvinsi, #filterKabupaten, #filterRuas').on('change', function() {
        table.draw(); // Reload DataTable
    });

    // Loading indicator
    table.on('processing.dt', function (e, settings, processing) {
        $('#loading').toggle(processing);
    });
});

// Fungsi untuk refresh manual
function refreshTable() {
    $('#drpTable').DataTable().ajax.reload();
}
</script>
@endpush
