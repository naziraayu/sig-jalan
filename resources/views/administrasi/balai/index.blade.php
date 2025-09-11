@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Balai Management</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </div>
                <div class="breadcrumb-item active">Balai</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Daftar Balai</h2>
            <p class="section-lead">
                Menampilkan seluruh data balai di sistem.
            </p>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Tabel Balai</h4>
                    <div>
                        @if(auth()->user()->hasPermission('delete','balai'))
                            <form action="{{ route('balai.destroyAll') }}" method="POST" class="d-inline" 
                                onsubmit="return confirm('Yakin ingin menghapus semua balai?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Hapus Semua
                                </button>
                            </form>
                        @endif
                        @if(auth()->user()->hasPermission('import','balai') || auth()->user()->hasPermission('export','balai'))
                            <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#modalImportExport">
                                <i class="fas fa-exchange-alt"></i> Import / Export
                            </button>
                        @endif
                        @if(auth()->user()->hasPermission('add','balai'))
                            <a href="{{ route('balai.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Balai
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
                        <table id="balaiTable" class="table table-striped table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Kode Balai</th>
                                    <th>Nama Balai</th>
                                    <th>Provinsi</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($balais as $balai)
                                <tr>
                                    <td>{{ $balai->balai_code }}</td>
                                    <td>{{ $balai->balai_name }}</td>
                                    <td>{{ $balai->province->province_name ?? '-' }}</td>
                                    <td>
                                        @if(auth()->user()->hasPermission('update','balai'))
                                            <a href="{{ route('balai.edit', $balai->balai_code) }}" 
                                            class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        @if(auth()->user()->hasPermission('delete','balai'))
                                            <form action="{{ route('balai.destroy', $balai->balai_code) }}" 
                                                method="POST" 
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus balai ini?')">
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
    'title' => 'Import / Export Balai',
    'importRoute' => route('balai.import'),
    'exportRoute' => route('balai.export'),
])

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#balaiTable').DataTable({
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
