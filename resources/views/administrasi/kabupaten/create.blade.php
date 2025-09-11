@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Tambah Kabupaten</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('kabupaten.index') }}">Kabupaten</a></div>
                <div class="breadcrumb-item">Tambah</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Form Tambah Kabupaten</h2>
            <p class="section-lead">Gunakan form ini untuk menambahkan kabupaten baru.</p>

            <div class="card">
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form action="{{ route('kabupaten.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Kode Kabupaten</label>
                            <input type="text" name="kabupaten_code" class="form-control" value="{{ old('kabupaten_code') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Nama Kabupaten</label>
                            <input type="text" name="kabupaten_name" class="form-control" value="{{ old('kabupaten_name') }}" required>
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

                        <div class="form-group">
                            <label>Balai</label>
                            <select name="balai_code" class="form-control">
                                <option value="">-- Pilih Balai --</option>
                                @foreach($balais as $balai)
                                    <option value="{{ $balai->balai_code }}" {{ old('balai_code') == $balai->balai_code ? 'selected' : '' }}>
                                        {{ $balai->balai_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Pulau</label>
                            <select name="island_code" class="form-control">
                                <option value="">-- Pilih Pulau --</option>
                                @foreach($islands as $island)
                                    <option value="{{ $island->island_code }}" {{ old('island_code') == $island->island_code ? 'selected' : '' }}>
                                        {{ $island->island_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Default Kabupaten</label>
                            <select name="default_kabupaten" class="form-control">
                                <option value="0" {{ old('default_kabupaten') == '0' ? 'selected' : '' }}>Tidak</option>
                                <option value="1" {{ old('default_kabupaten') == '1' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Stable</label>
                            <select name="stable" class="form-control">
                                <option value="0" {{ old('stable') == '0' ? 'selected' : '' }}>Tidak</option>
                                <option value="1" {{ old('stable') == '1' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('kabupaten.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
