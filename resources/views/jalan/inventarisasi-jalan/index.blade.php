@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Inventarisasi Ruas Jalan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item active">Ruas Jalan</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Daftar Ruas Jalan</h2>
            <p class="section-lead">Menampilkan data ruas jalan berdasarkan pilihan dropdown.</p>

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

                        {{-- Ruas --}}
                        <div class="form-group col-md-3">
                            <label for="filterRuas">Pilih Ruas</label>
                            <select id="filterRuas" class="form-control">
                                <option value="">-- Pilih Ruas --</option>
                                @foreach($ruasjalan as $ruas)
                                    <option value="{{ $ruas->link_no }}">
                                        {{ $ruas->linkNo?->link_code }} - {{ $ruas->linkNo?->link_name }}
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
                    <h4>Data Inventarisasi Jalan</h4>
                    <div class="d-flex flex-wrap gap-2">

                        {{-- Hapus Semua --}}
                        @if(auth()->user()->hasPermission('delete','inventarisasi_jalan'))
                            <form action="{{ route('inventarisasi-jalan.destroyAll') }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus semua data inventarisasi? Semua data akan hilang!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon icon-left btn-danger">
                                    <i class="fas fa-trash"></i> Hapus Semua
                                </button>
                            </form>
                        @endif

                        {{-- Import / Export --}}
                        @if(auth()->user()->hasPermission('import','inventarisasi_jalan') || auth()->user()->hasPermission('export','inventarisasi_jalan'))
                            <button type="button" class="btn btn-icon icon-left btn-success" data-toggle="modal" data-target="#modalImportExport">
                                <i class="fas fa-file-excel"></i> Import / Export
                            </button>
                        @endif

                        {{-- Tambah Data --}}
                        @if(auth()->user()->hasPermission('add','inventarisasi_jalan'))
                            <a href="{{ route('inventarisasi-jalan.create') }}" class="btn btn-icon icon-left btn-primary">
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
    'title' => 'Import / Export Ruas Jalan',
    'importRoute' => route('inventarisasi-jalan.import'),
    'exportRoute' => route('inventarisasi-jalan.export'),
]) 

@endsection

@push('scripts')
<script>
$(document).ready(function(){
    $('#filterRuas').on('change', function(){
        let linkNo = $(this).val();
        if(linkNo){
            $.ajax({
                url: "{{ route('inventarisasi-jalan.getDetail') }}",
                type: "GET",
                data: {link_no: linkNo},
                success: function(res){
                    if(res.success){
                        // Buat struktur tabel datatables
                        let html = `
                            <table id="detailRuasTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Chainage From</th>
                                        <th>Chainage To</th>
                                        <th>Tipe Perkerasan</th>
                                        <th>ROW</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        res.data.forEach(function(item){
                            console.log('Full item:', item);
                            console.log('item.link_no:', item.link_no);
                            console.log('typeof item.link_no:', typeof item.link_no);
                            
                            // Pastikan link_no adalah string
                            let linkNo = item.link_no;
                            if (typeof linkNo === 'object' && linkNo !== null) {
                                console.log('link_no is object, trying to get string value');
                                // Jika link_no adalah object, coba ambil properti yang sesuai
                                linkNo = linkNo.link_no || linkNo.id || linkNo.value || linkNo;
                            }
                            
                            console.log('Final linkNo:', linkNo);
                            
                            html += `
                                <tr>
                                    <td>${item.chainage_from}</td>
                                    <td>${item.chainage_to}</td>
                                    <td>${item.pavement_type?.code_description_ind ?? '-'}</td>
                                    <td>${item.row ?? '-'}</td>
                                    <td>
                                        <a href="/inventarisasi-jalan/show/${linkNo}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
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
                            pageLength: 5,
                            lengthMenu: [5, 10, 25, 50, 100]
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
});
</script>
@endpush