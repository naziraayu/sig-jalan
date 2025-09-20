@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Detail Ruas Jalan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </div>
                <div class="breadcrumb-item">
                    <a href="{{ route('ruas-jalan.index') }}">Ruas Jalan</a>
                </div>
                <div class="breadcrumb-item active">Detail</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Informasi Detail Ruas Jalan</h2>
            <p class="section-lead">Berikut adalah detail lengkap data ruas jalan.</p>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <tbody>
                                <tr>
                                    <th width="30%">Link No</th>
                                    <td>{{ $ruas->link_no }}</td>
                                </tr>
                                <tr>
                                    <th>Kode Ruas (Link Code)</th>
                                    <td>{{ $ruas->link_code }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Ruas</th>
                                    <td>{{ $ruas->link_name }}</td>
                                </tr>
                                <tr>
                                    <th>Provinsi</th>
                                    <td>{{ $ruas->province?->province_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Kabupaten</th>
                                    <td>{{ $ruas->kabupaten?->kabupaten_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status Ruas</th>
                                    <td>{{ $ruas->statusRelation?->code_description_ind ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Fungsi Ruas</th>
                                    <td>{{ $ruas->functionRelation?->code_description_ind ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Panjang Ruas (SK)</th>
                                    <td>{{ $ruas->link_length_official }}</td>
                                </tr>
                                <tr>
                                    <th>Panjang Ruas (Survei)</th>
                                    <td>{{ $ruas->link_length_actual }}</td>
                                </tr>
                                <tr>
                                    <th>Akses ke Jalan</th>
                                    <td>{{ $ruas->access_status ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('ruas-jalan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>

                        @if(auth()->user()->hasPermission('update','ruas_jalan'))
                            <a href="{{ route('ruas-jalan.edit', $ruas) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Data
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
