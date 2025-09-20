@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Ruas Jalan Management</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </div>
                <div class="breadcrumb-item active">Ruas Jalan</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Daftar Ruas Jalan</h2>
            <p class="section-lead">
                Menampilkan seluruh data ruas jalan di sistem.
            </p>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h4 class="mb-2 mb-md-0">Tabel Ruas Jalan</h4>

                    <div class="d-flex justify-content-between align-items-center flex-wrap w-100 mt-2 mt-md-0">
                        {{-- Dropdown filter kiri --}}
                        <div class="d-flex gap-2">
                            <select id="filterProvinsi" class="custom-select" style="border-radius: 0; min-width: 180px;">
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinsi as $prov)
                                    <option value="{{ $prov->province_code }}"
                                        {{ $prov->province_code == $defaultProvinsi ? 'selected' : '' }}>
                                        {{ $prov->province_name }}
                                    </option>
                                @endforeach
                            </select>

                            <select id="filterKabupaten" class="custom-select" style="border-radius: 0; min-width: 180px;">
                                <option value="">-- Pilih Kabupaten --</option>
                                @foreach($kabupaten as $kab)
                                    <option value="{{ $kab->kabupaten_code }}"
                                        {{ $kab->kabupaten_code == $defaultKabupaten ? 'selected' : '' }}>
                                        {{ $kab->kabupaten_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tombol aksi kanan --}}
                        <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                            @if(auth()->user()->hasPermission('delete','ruas_jalan'))
                                <form action="{{ route('ruas-jalan.destroyAll') }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus semua ruas jalan? Semua data akan hilang!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Hapus Semua Data
                                    </button>
                                </form>
                            @endif

                            @if(auth()->user()->hasPermission('import','ruas_jalan') || auth()->user()->hasPermission('export','ruas_jalan'))
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalImportExport">
                                    <i class="fas fa-exchange-alt"></i> Import / Export
                                </button>
                            @endif

                            @if(auth()->user()->hasPermission('add','ruas_jalan'))
                                <a href="{{ route('ruas-jalan.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tambah Ruas Jalan
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
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- Alert error --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="ruasJalanTable" class="table table-striped table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Status Ruas</th>
                                    <th>Kode Provinsi</th>
                                    <th>Kode Kabupaten</th>
                                    <th>Nomor Ruas</th>
                                    <th>Nama Ruas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ruasjalan as $ruas)
                                    <tr data-provinsi="{{ $ruas->province?->province_code }}"
                                        data-kabupaten="{{ $ruas->kabupaten?->kabupaten_code }}">
                                        
                                        <td>{{ $ruas->statusRelation?->code ?? '-' }}</td>
                                        <td>{{ $ruas->province?->province_code ?? '-' }}</td>
                                        <td>{{ $ruas->kabupaten?->kabupaten_code ?? '-' }}</td>
                                        <td>{{ $ruas->link_code }}</td>
                                        <td>{{ $ruas->link_name }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                {{-- Tombol Detail --}}
                                                @if(auth()->user()->hasPermission('detail','ruas_jalan'))
                                                    <a href="{{ route('ruas-jalan.show', $ruas) }}" 
                                                    class="btn btn-info btn-sm" title="Detail Data">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif

                                                {{-- Tombol Edit --}}
                                                @if(auth()->user()->hasPermission('update','ruas_jalan'))
                                                    <a href="{{ route('ruas-jalan.edit', $ruas) }}" 
                                                    class="btn btn-warning btn-sm" title="Edit Data">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif

                                                {{-- Tombol Hapus --}}
                                                @if(auth()->user()->hasPermission('delete','ruas_jalan'))
                                                    <form action="{{ route('ruas-jalan.destroy', $ruas) }}" 
                                                        method="POST" 
                                                        class="d-inline"
                                                        onsubmit="return confirm('Yakin ingin menghapus ruas jalan ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-danger btn-sm" 
                                                                title="Hapus Data">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
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
    'title' => 'Import / Export Ruas Jalan',
    'importRoute' => route('ruas-jalan.import'),
    'exportRoute' => route('ruas-jalan.export'),
]) 

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let table = $('#ruasJalanTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            pageLength: 10,
        });

        // Custom filter berdasarkan provinsi & kabupaten
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            let provFilter = $('#filterProvinsi').val();
            let kabFilter  = $('#filterKabupaten').val();

            let rowProv = $(table.row(dataIndex).node()).data('provinsi');
            let rowKab  = $(table.row(dataIndex).node()).data('kabupaten');

            if ((provFilter === "" || rowProv == provFilter) &&
                (kabFilter === "" || rowKab == kabFilter)) {
                return true;
            }
            return false;
        });

        // Trigger filter
        $('#filterProvinsi, #filterKabupaten').on('change', function() {
            table.draw();
        });

        // Jalankan filter default (sesuai defaultProvinsi & defaultKabupaten)
        table.draw();
    });
</script>
@endpush
