@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Provinsi</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('provinces.index') }}">Provinsi</a></div>
                <div class="breadcrumb-item">Edit</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Form Edit Provinsi</h2>
            <p class="section-lead">Ubah data provinsi sesuai kebutuhan.</p>

            <div class="card">
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form action="{{ route('provinces.update', $province) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Kode Provinsi</label>
                            <input type="text" value="{{ $province->province_code }}" class="form-control" disabled>
                        </div>

                        <div class="form-group">
                            <label>Nama Provinsi</label>
                            <input type="text" name="province_name" value="{{ old('province_name', $province->province_name) }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Default Province</label>
                            <select name="default_province" class="form-control">
                                <option value="0" {{ $province->default_province ? '' : 'selected' }}>Tidak</option>
                                <option value="1" {{ $province->default_province ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Stable</label>
                            <select name="stable" class="form-control">
                                <option value="0" {{ $province->stable ? '' : 'selected' }}>Tidak</option>
                                <option value="1" {{ $province->stable ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('provinces.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
