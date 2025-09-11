@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Kabupaten Management</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </div>
                <div class="breadcrumb-item active">Kabupaten</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Daftar Kabupaten</h2>
            <p class="section-lead">
                Menampilkan seluruh data kabupaten di sistem.
            </p>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Tabel Kabupaten</h4>
                    <div>
                        @if(auth()->user()->hasPermission('delete','kabupaten'))
                            <form action="{{ route('kabupaten.destroyAll') }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus semua kabupaten?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Hapus Semua Data
                                </button>
                            </form>
                        @endif
                        @if(auth()->user()->hasPermission('import','kabupaten') || auth()->user()->hasPermission('export','kabupaten'))
                            <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#modalImportExport">
                                <i class="fas fa-exchange-alt"></i> Import / Export
                            </button>
                        @endif
                        @if(auth()->user()->hasPermission('add','kabupaten'))
                            <a href="{{ route('kabupaten.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Kabupaten
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
                        <table id="kabupatenTable" class="table table-striped table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Kabupaten</th>
                                    <th>Provinsi</th>
                                    <th>Balai</th>
                                    <th>Default</th>
                                    <th>Stable</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kabupatens as $kabupaten)
                                <tr>
                                    <td>{{ $kabupaten->kabupaten_code }}</td>
                                    <td>{{ $kabupaten->kabupaten_name }}</td>
                                    <td>{{ $kabupaten->province?->province_name ?? '-' }}</td>
                                    <td>{{ $kabupaten->balai?->balai_name ?? '-' }}</td>
                                    <td>
                                        @if($kabupaten->default_kabupaten)
                                            <span class="badge badge-primary">Ya</span>
                                        @else
                                            <span class="badge badge-secondary">Tidak</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($kabupaten->stable)
                                            <span class="badge badge-success">Stable</span>
                                        @else
                                            <span class="badge badge-warning">Unstable</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(auth()->user()->hasPermission('update','kabupaten'))
                                            <a href="{{ route('kabupaten.edit', $kabupaten->kabupaten_code) }}" 
                                            class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        @if(auth()->user()->hasPermission('delete','kabupaten'))
                                            <form action="{{ route('kabupaten.destroy', $kabupaten->kabupaten_code) }}" 
                                                method="POST" 
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus kabupaten ini?')">
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
                                    <td colspan="7" class="text-center">Tidak ada data</td>
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
    'title' => 'Import / Export Kabupaten',
    'importRoute' => route('kabupaten.import'),
    'exportRoute' => route('kabupaten.export'),
])

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#kabupatenTable').DataTable({
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
