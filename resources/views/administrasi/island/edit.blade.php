@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Pulau</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('island.index') }}">Pulau</a></div>
                <div class="breadcrumb-item">Edit</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Form Edit Pulau</h2>
            <p class="section-lead">Gunakan form ini untuk memperbarui data pulau.</p>

            <div class="card">
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form action="{{ route('island.update', $island->island_code) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Kode Pulau</label>
                            <input type="text" class="form-control" value="{{ $island->island_code }}" disabled>
                        </div>

                        <div class="form-group">
                            <label>Nama Pulau</label>
                            <input type="text" name="island_name" class="form-control" value="{{ old('island_name', $island->island_name) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Provinsi</label>
                            <select name="province_code" class="form-control" required>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->province_code }}" 
                                        {{ old('province_code', $island->province_code) == $province->province_code ? 'selected' : '' }}>
                                        {{ $province->province_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('island.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
