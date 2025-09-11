@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Tambah Kecamatan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('kecamatan.index') }}">Kecamatan</a></div>
                <div class="breadcrumb-item">Tambah</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Form Tambah Kecamatan</h2>
            <p class="section-lead">Gunakan form ini untuk menambahkan kecamatan baru.</p>

            <div class="card">
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form action="{{ route('kecamatan.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Kode Kecamatan</label>
                            <input type="text" name="kecamatan_code" class="form-control" value="{{ old('kecamatan_code') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Nama Kecamatan</label>
                            <input type="text" name="kecamatan_name" class="form-control" value="{{ old('kecamatan_name') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Kabupaten</label>
                            <select name="kabupaten_code" class="form-control" required>
                                <option value="">-- Pilih Kabupaten --</option>
                                @foreach($kabupatens as $kabupaten)
                                    <option value="{{ $kabupaten->kabupaten_code }}" {{ old('kabupaten_code') == $kabupaten->kabupaten_code ? 'selected' : '' }}>
                                        {{ $kabupaten->kabupaten_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Provinsi</label>
                            <select name="province_code" class="form-control" required>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->province_code }}" {{ old('province_code') == $province->province_code ? 'selected' : '' }}>
                                        {{ $province->province_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('kecamatan.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
