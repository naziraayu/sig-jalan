@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Balai</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('balai.index') }}">Balai</a></div>
                <div class="breadcrumb-item">Edit</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Form Edit Balai</h2>
            <p class="section-lead">Gunakan form ini untuk mengubah data balai.</p>

            <div class="card">
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form action="{{ route('balai.update', $balai->balai_code) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Kode Balai</label>
                            <input type="text" name="balai_code" class="form-control" 
                                   value="{{ old('balai_code', $balai->balai_code) }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Nama Balai</label>
                            <input type="text" name="balai_name" class="form-control" 
                                   value="{{ old('balai_name', $balai->balai_name) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Provinsi</label>
                            <select name="province_code" class="form-control" required>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->province_code }}" 
                                        {{ old('province_code', $balai->province_code) == $province->province_code ? 'selected' : '' }}>
                                        {{ $province->province_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('balai.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
