@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Island Management</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </div>
                <div class="breadcrumb-item active">Island</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Daftar Pulau</h2>
            <p class="section-lead">
                Menampilkan seluruh data pulau di sistem.
            </p>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Tabel Pulau</h4>
                    <div>
                         @if(auth()->user()->hasPermission('delete','pulau'))
                            <form action="{{ route('island.destroyAll') }}" method="POST" class="d-inline" 
                                onsubmit="return confirm('Yakin ingin menghapus semua pulau?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Hapus Semua
                                </button>
                            </form>
                        @endif
                        @if(auth()->user()->hasPermission('import','pulau') || auth()->user()->hasPermission('export','pulau'))
                            <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#modalImportExport">
                                <i class="fas fa-exchange-alt"></i> Import / Export
                            </button>
                        @endif
                        @if(auth()->user()->hasPermission('add','pulau'))
                            <a href="{{ route('island.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Pulau
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
                        <table id="islandTable" class="table table-striped table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Kode Pulau</th>
                                    <th>Nama Pulau</th>
                                    <th>Provinsi</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($islands as $island)
                                <tr>
                                    <td>{{ $island->island_code }}</td>
                                    <td>{{ $island->island_name }}</td>
                                    <td>{{ $island->province->province_name ?? '-' }}</td>
                                    <td>
                                        @if(auth()->user()->hasPermission('update','pulau'))
                                            <a href="{{ route('island.edit', $island->island_code) }}" 
                                            class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        @if(auth()->user()->hasPermission('delete','pulau'))
                                            <form action="{{ route('island.destroy', $island->island_code) }}" 
                                                method="POST" 
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus pulau ini?')">
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
                                    <td colspan="4" class="text-center">Tidak ada data</td>
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
    'title' => 'Import / Export Pulau',
    'importRoute' => route('island.import'),
    'exportRoute' => route('island.export'),
])

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#islandTable').DataTable({
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
