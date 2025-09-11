@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Tambah Provinsi</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('provinces.index') }}">Provinsi</a></div>
                <div class="breadcrumb-item">Tambah</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Form Tambah Provinsi</h2>
            <p class="section-lead">Gunakan form ini untuk menambahkan provinsi baru.</p>

            <div class="card">
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form action="{{ route('provinces.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Kode Provinsi</label>
                            <input type="text" name="province_code" class="form-control" value="{{ old('province_code') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Nama Provinsi</label>
                            <input type="text" name="province_name" class="form-control" value="{{ old('province_name') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Default Province</label>
                            <select name="default_province" class="form-control">
                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Stable</label>
                            <select name="stable" class="form-control">
                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('provinces.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
