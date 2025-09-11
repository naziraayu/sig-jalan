@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Province Management</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                </div>
                <div class="breadcrumb-item active">Provinces</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Daftar Provinsi</h2>
            <p class="section-lead">
                Menampilkan seluruh data provinsi di sistem.
            </p>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Tabel Provinsi</h4>
                    <div>
                        @if(auth()->user()->hasPermission('delete','provinsi'))
                            <form action="{{ route('provinces.destroyAll') }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus semua provinsi? Semua data akan hilang!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Hapus Semua Data
                                </button>
                            </form>
                        @endif
                        @if(auth()->user()->hasPermission('import','provinsi') || auth()->user()->hasPermission('export','provinsi'))
                            <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#modalImportExport">
                                <i class="fas fa-exchange-alt"></i> Import / Export
                            </button>
                        @endif
                        @if(auth()->user()->hasPermission('add','provinsi'))
                            <a href="{{ route('provinces.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Provinsi
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
                        <table id="provinceTable" class="table table-striped table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    {{-- <th width="5%">#</th> --}}
                                    <th>Kode</th>
                                    <th>Nama Provinsi</th>
                                    <th>Default</th>
                                    <th>Stable</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($provinces as $province)
                                <tr>
                                    {{-- <td>{{ $loop->iteration }}</td> --}}
                                    <td>{{ $province->province_code }}</td>
                                    <td>{{ $province->province_name }}</td>
                                    <td>
                                        @if($province->default_province)
                                            <span class="badge badge-primary">Ya</span>
                                        @else
                                            <span class="badge badge-secondary">Tidak</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($province->stable)
                                            <span class="badge badge-success">Stable</span>
                                        @else
                                            <span class="badge badge-warning">Unstable</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(auth()->user()->hasPermission('update','provinsi'))
                                            <a href="{{ route('provinces.edit', $province->province_code) }}" 
                                            class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        @if(auth()->user()->hasPermission('delete','provinsi'))
                                            <form action="{{ route('provinces.destroy', $province->province_code) }}" 
                                                method="POST" 
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus provinsi ini?')">
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
                                    <td colspan="6" class="text-center">Tidak ada data</td>
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

{{-- Include modal reusable --}}
@include('components.modals.import_export', [
    'title' => 'Import / Export Provinsi',
    'importRoute' => route('provinces.import'),
    'exportRoute' => route('provinces.export'),
])

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#provinceTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthChange: true,
        pageLength: 10,
        });
    });
</script>
@endpush
