@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Inventarisasi Ruas Jalan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item active">Inventarisasi Jalan</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Daftar Inventarisasi Ruas Jalan</h2>
            <p class="section-lead">
                Menampilkan data inventarisasi ruas jalan berdasarkan tahun yang dipilih
                @if($selectedYear)
                    <span class="badge badge-primary">Tahun: {{ $selectedYear }}</span>
                @else
                    <span class="badge badge-warning">Pilih tahun terlebih dahulu di filter kanan atas</span>
                @endif
            </p>

            {{-- Alert jika belum pilih tahun --}}
            @if(!$selectedYear)
                <div class="alert alert-warning alert-has-icon">
                    <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="alert-body">
                        <div class="alert-title">Perhatian!</div>
                        Silakan pilih tahun terlebih dahulu menggunakan filter tahun di pojok kanan atas untuk menampilkan data inventarisasi jalan.
                    </div>
                </div>
            @endif

            {{-- Card Filter --}}
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-filter"></i> Filter Ruas Jalan</h4>
                </div>
                <div class="card-body">
                    <div class="form-row">

                        {{-- Status Ruas --}}
                        <div class="form-group col-md-3">
                            <label for="filterStatus">Status Ruas</label>
                            <select id="filterStatus" class="form-control" disabled>
                                @foreach($statusRuas as $status)
                                    <option value="{{ $status->code }}"
                                        {{ $status->code == 'K' ? 'selected' : '' }}>
                                        {{ $status->code_description_ind }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Status: Kabupaten (Fixed)</small>
                        </div>

                        {{-- Provinsi --}}
                        <div class="form-group col-md-3">
                            <label for="filterProvinsi">Provinsi</label>
                            <select id="filterProvinsi" class="form-control" disabled>
                                @foreach($provinsi as $prov)
                                    <option value="{{ $prov->province_code }}"
                                        {{ $prov->province_name == 'Jawa Timur' ? 'selected' : '' }}>
                                        {{ $prov->province_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Provinsi: Jawa Timur (Fixed)</small>
                        </div>

                        {{-- Kabupaten --}}
                        <div class="form-group col-md-3">
                            <label for="filterKabupaten">Kabupaten</label>
                            <select id="filterKabupaten" class="form-control">
                                @foreach($kabupaten as $kab)
                                    <option value="{{ $kab->kabupaten_code }}"
                                        {{ $kab->kabupaten_name == 'Jember' ? 'selected' : '' }}>
                                        {{ $kab->kabupaten_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ruas Jalan --}}
                        <div class="form-group col-md-3">
                            <label for="filterRuas">Pilih Ruas Jalan <span class="text-danger">*</span></label>
                            <select id="filterRuas" class="form-control" @if(!$selectedYear) disabled @endif>
                                <option value="">-- Pilih Ruas --</option>
                                @if($selectedYear)
                                    @foreach($ruasjalan as $ruas)
                                        <option value="{{ $ruas->link_no }}" data-link-id="{{ $ruas->id }}">
                                            {{ $ruas->linkMaster?->link_code ?? $ruas->link_no }} - 
                                            {{ $ruas->linkMaster?->link_name ?? 'Tidak ada nama' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @if(!$selectedYear)
                                <small class="text-danger">Pilih tahun terlebih dahulu</small>
                            @else
                                <small class="text-muted">{{ $ruasjalan->count() }} ruas tersedia untuk tahun {{ $selectedYear }}</small>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

            {{-- Card Data Inventarisasi --}}
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h4><i class="fas fa-table"></i> Data Inventarisasi Jalan</h4>
                    <div class="d-flex flex-wrap gap-2">

                        {{-- Tombol Hapus Semua --}}
                        @if(auth()->user()->hasPermission('delete','inventarisasi_jalan'))
                            @if($selectedYear)
                                <form action="{{ route('inventarisasi-jalan.destroyAll') }}" method="POST" class="d-inline mr-2"
                                    onsubmit="return confirm('⚠️ PERHATIAN!\n\nAnda akan menghapus SEMUA data inventarisasi jalan tahun {{ $selectedYear }}.\n\nData yang dihapus tidak dapat dikembalikan!\n\nLanjutkan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon icon-left btn-danger">
                                        <i class="fas fa-trash"></i> Hapus Semua ({{ $selectedYear }})
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-icon icon-left btn-secondary mr-2" disabled 
                                    title="Pilih tahun terlebih dahulu">
                                    <i class="fas fa-trash"></i> Hapus Semua
                                </button>
                            @endif
                        @endif

                        {{-- Tombol Import / Export --}}
                        @if(auth()->user()->hasPermission('import','inventarisasi_jalan') || auth()->user()->hasPermission('export','inventarisasi_jalan'))
                            <button type="button" class="btn btn-icon icon-left btn-success mr-2" 
                                data-toggle="modal" data-target="#modalImportExport"
                                @if(!$selectedYear) disabled title="Pilih tahun terlebih dahulu" @endif>
                                <i class="fas fa-file-excel"></i> Import / Export
                            </button>
                        @endif

                        {{-- Tombol Tambah Data --}}
                        @if(auth()->user()->hasPermission('add','inventarisasi_jalan'))
                            <a href="{{ route('inventarisasi-jalan.create') }}" 
                               class="btn btn-icon icon-left btn-primary"
                               @if(!$selectedYear) 
                                   onclick="event.preventDefault(); alert('Silakan pilih tahun terlebih dahulu!');" 
                                   style="opacity: 0.6; cursor: not-allowed;"
                               @endif>
                                <i class="fas fa-plus"></i> Tambah Data
                            </a>
                        @endif

                    </div>
                </div>

                <div class="card-body">
                    {{-- Area untuk menampilkan data --}}
                    <div id="detailRuas">
                        @if(!$selectedYear)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Informasi:</strong> Silakan pilih tahun di filter kanan atas, kemudian pilih ruas jalan untuk menampilkan data inventarisasi.
                            </div>
                        @else
                            <p class="text-muted text-center py-4">
                                <i class="fas fa-arrow-up"></i> Silakan pilih ruas jalan pada dropdown di atas untuk menampilkan data inventarisasi
                            </p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

{{-- Modal Import Export --}}
@include('components.modals.import_export', [
    'title' => 'Import / Export Inventarisasi Jalan' . ($selectedYear ? ' - Tahun ' . $selectedYear : ''),
    'importRoute' => route('inventarisasi-jalan.import'),
    'exportRoute' => route('inventarisasi-jalan.export'),
]) 

@endsection

@push('scripts')
<script>
$(document).ready(function(){
    
    // ✅ Variable untuk menyimpan DataTable instance
    let dataTable = null;
    
    // ✅ Fungsi untuk destroy DataTable jika sudah ada
    function destroyDataTable() {
        if (dataTable !== null) {
            dataTable.destroy();
            dataTable = null;
        }
    }

    // ✅ Event handler ketika ruas dipilih
    $('#filterRuas').on('change', function(){
        let linkNo = $(this).val();
        
        // Destroy DataTable sebelum load data baru
        destroyDataTable();
        
        if(linkNo){
            $.ajax({
                url: "{{ route('inventarisasi-jalan.getDetail') }}",
                type: "GET",
                data: {link_no: linkNo},
                beforeSend: function() {
                    $('#detailRuas').html(`
                        <div class="text-center py-4">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="mt-2">Memuat data inventarisasi...</p>
                        </div>
                    `);
                },
                success: function(res){
                    if(res.success && res.data.length > 0){
                        
                        // ✅ Info header ruas
                        let firstData = res.data[0];
                        let ruasInfo = firstData.link?.link_master 
                            ? `${firstData.link.link_master.link_code} - ${firstData.link.link_master.link_name}`
                            : 'Ruas ' + linkNo;
                        
                        // ✅ Buat struktur tabel
                        let html = `
                            <div class="alert alert-success alert-has-icon mb-3">
                                <div class="alert-icon"><i class="fas fa-road"></i></div>
                                <div class="alert-body">
                                    <div class="alert-title">Data Ditemukan</div>
                                    Menampilkan <strong>${res.data.length} segmen</strong> inventarisasi untuk ruas: <strong>${ruasInfo}</strong>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table id="detailRuasTable" class="table table-striped table-bordered table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th>Chainage From (Km)</th>
                                            <th>Chainage To (Km)</th>
                                            <th>Panjang (m)</th>
                                            <th>Tipe Perkerasan</th>
                                            <th>Lebar (m)</th>
                                            <th>ROW (m)</th>
                                            <th>Status</th>
                                            <th style="width: 100px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        // ✅ Loop data
                        res.data.forEach(function(item, index){
                            let panjang = (item.chainage_to - item.chainage_from) * 1000; // Convert to meters
                            let statusBadge = item.impassable == 1 
                                ? '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Tidak Dapat Dilalui</span>' 
                                : '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Dapat Dilalui</span>';
                            
                            let linkNoValue = item.link?.link_no ?? linkNo;
                            let detailUrl = "{{ route('inventarisasi-jalan.show', ':linkNo') }}".replace(':linkNo', linkNoValue);
                            let editUrl = "{{ route('inventarisasi-jalan.edit', ':linkNo') }}".replace(':linkNo', linkNoValue);

                            html += `
                                <tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td>${parseFloat(item.chainage_from).toFixed(3)}</td>
                                    <td>${parseFloat(item.chainage_to).toFixed(3)}</td>
                                    <td class="text-right">${panjang.toFixed(2)}</td>
                                    <td>${item.pavement_type?.code_description_ind ?? '-'}</td>
                                    <td class="text-right">${item.pave_width ? parseFloat(item.pave_width).toFixed(2) : '-'}</td>
                                    <td class="text-right">${item.row ? parseFloat(item.row).toFixed(2) : '-'}</td>
                                    <td class="text-center">${statusBadge}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="${detailUrl}" 
                                            class="btn btn-sm btn-info" 
                                            title="Lihat Detail Lengkap">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(auth()->user()->hasPermission('update','inventarisasi_jalan'))
                                                <a href="${editUrl}"
                                                class="btn btn-sm btn-warning" 
                                                title="Edit Data">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </div>
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

                        // ✅ Inisialisasi DataTables
                        dataTable = $('#detailRuasTable').DataTable({
                            responsive: true,
                            autoWidth: false,
                            pageLength: 10,
                            lengthMenu: [5, 10, 25, 50, 100],
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
                                search: "_INPUT_",
                                searchPlaceholder: "Cari data..."
                            },
                            columnDefs: [
                                { orderable: false, targets: [0, 8] }, // No & Aksi tidak bisa sort
                                { className: "text-center", targets: [0, 7, 8] },
                                { className: "text-right", targets: [3, 5, 6] }
                            ],
                            order: [[1, 'asc']], // Sort by Chainage From
                            dom: 'Bfrtip',
                            buttons: [
                                {
                                    extend: 'excel',
                                    text: '<i class="fas fa-file-excel"></i> Excel',
                                    className: 'btn btn-success btn-sm',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                                    }
                                },
                                {
                                    extend: 'pdf',
                                    text: '<i class="fas fa-file-pdf"></i> PDF',
                                    className: 'btn btn-danger btn-sm',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                                    }
                                },
                                {
                                    extend: 'print',
                                    text: '<i class="fas fa-print"></i> Print',
                                    className: 'btn btn-info btn-sm',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                                    }
                                }
                            ]
                        });
                        
                    } else {
                        $('#detailRuas').html(`
                            <div class="alert alert-warning alert-has-icon">
                                <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                                <div class="alert-body">
                                    <div class="alert-title">Data Tidak Ditemukan</div>
                                    Tidak ada data inventarisasi untuk ruas ini pada tahun <strong>{{ $selectedYear ?? 'yang dipilih' }}</strong>.
                                </div>
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    
                    $('#detailRuas').html(`
                        <div class="alert alert-danger alert-has-icon">
                            <div class="alert-icon"><i class="fas fa-exclamation-circle"></i></div>
                            <div class="alert-body">
                                <div class="alert-title">Terjadi Kesalahan</div>
                                Gagal memuat data inventarisasi. Silakan coba lagi atau hubungi administrator.
                            </div>
                        </div>
                    `);
                }
            });
        } else {
            $('#detailRuas').html(`
                <p class="text-muted text-center py-4">
                    <i class="fas fa-arrow-up"></i> Silakan pilih ruas jalan pada dropdown di atas untuk menampilkan data inventarisasi
                </p>
            `);
        }
    });
    
    // ✅ Optional: Auto-load jika ada ruas terpilih (misalnya dari session/query string)
    @if(request()->has('link_no'))
        $('#filterRuas').val('{{ request()->get("link_no") }}').trigger('change');
    @endif
    
});
</script>
@endpush