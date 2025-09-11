@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Kecamatan Management</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </div>
                <div class="breadcrumb-item active">Kecamatan</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Daftar Kecamatan</h2>
            <p class="section-lead">
                Menampilkan seluruh data kecamatan di sistem.
            </p>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Tabel Kecamatan</h4>
                    <div>
                        @if(auth()->user()->hasPermission('delete','kecamatan'))
                            <form action="{{ route('kecamatan.destroyAll') }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus semua kecamatan?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Hapus Semua Data
                                </button>
                            </form>
                        @endif
                        @if(auth()->user()->hasPermission('import','kecamatan') || auth()->user()->hasPermission('export','kecamatan'))
                            <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#modalImportExport">
                                <i class="fas fa-exchange-alt"></i> Import / Export
                            </button>
                        @endif
                        @if(auth()->user()->hasPermission('add','kecamatan'))
                            <a href="{{ route('kecamatan.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Kecamatan
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="kecamatanTable" class="table table-striped table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Kecamatan</th>
                                    <th>Kabupaten</th>
                                    <th>Provinsi</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kecamatans as $kecamatan)
                                <tr>
                                    <td>{{ $kecamatan->kecamatan_code }}</td>
                                    <td>{{ $kecamatan->kecamatan_name }}</td>
                                    <td>{{ $kecamatan->kabupaten?->kabupaten_name ?? '-' }}</td>
                                    <td>{{ $kecamatan->province?->province_name ?? '-' }}</td>
                                    <td>
                                        @if(auth()->user()->hasPermission('update','kecamatan'))
                                            <a href="{{ route('kecamatan.edit', $kecamatan->kecamatan_code) }}" 
                                            class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        @if(auth()->user()->hasPermission('delete','kecamatan'))
                                            <form action="{{ route('kecamatan.destroy', $kecamatan->kecamatan_code) }}" 
                                                method="POST" 
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus kecamatan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data</td>
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
    'title' => 'Import / Export Kecamatan',
    'importRoute' => route('kecamatan.import'),
    'exportRoute' => route('kecamatan.export'),
])

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#kecamatanTable').DataTable();
    });
</script>
@endpush
