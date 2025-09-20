@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Ruas Jalan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </div>
                <div class="breadcrumb-item">
                    <a href="{{ route('ruas-jalan.index') }}">Ruas Jalan</a>
                </div>
                <div class="breadcrumb-item">Edit</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Form Edit Ruas Jalan</h2>
            <p class="section-lead">Gunakan form ini untuk memperbarui data ruas jalan.</p>

            <div class="card">
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('ruas-jalan.update', $ruas) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Link No</label>
                                <input type="text" name="link_no" 
                                    class="form-control" 
                                    value="{{ old('link_no', $ruas->link_no) }}" readonly>
                            </div>

                            <div class="form-group col-md-3">
                                <label>Kode Ruas (Link Code)</label>
                                <input type="text" name="link_code" 
                                    class="form-control" 
                                    value="{{ old('link_code', $ruas->link_code) }}" readonly>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Nama Ruas</label>
                                <input type="text" name="link_name" 
                                    class="form-control" 
                                    value="{{ old('link_name', $ruas->link_name) }}" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Provinsi</label>
                                <select name="province_code" class="form-control" required>
                                    <option value="">-- Pilih Provinsi --</option>
                                    @foreach($provinsi as $prov)
                                        <option value="{{ $prov->province_code }}"
                                            {{ old('province_code', $ruas->province_code) == $prov->province_code ? 'selected' : '' }}>
                                            {{ $prov->province_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Kabupaten</label>
                                <select name="kabupaten_code" class="form-control" required>
                                    <option value="">-- Pilih Kabupaten --</option>
                                    @foreach($kabupaten as $kab)
                                        <option value="{{ $kab->kabupaten_code }}"
                                            {{ old('kabupaten_code', $ruas->kabupaten_code) == $kab->kabupaten_code ? 'selected' : '' }}>
                                            {{ $kab->kabupaten_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Status Ruas</label>
                                <select name="status" class="form-control">
                                    <option value="">-- Pilih Status --</option>
                                    @foreach($statusList as $status)
                                        <option value="{{ $status->code }}"
                                            {{ old('status', $ruas->status) == $status->code ? 'selected' : '' }}>
                                            {{ $status->code_description_ind }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Fungsi Ruas</label>
                                <select name="function" class="form-control">
                                    <option value="">-- Pilih Fungsi --</option>
                                    @foreach($functionList as $func)
                                        <option value="{{ $func->code }}"
                                            {{ old('function', $ruas->function) == $func->code ? 'selected' : '' }}>
                                            {{ $func->code_description_ind }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Panjang Ruas (SK)</label>
                                <input type="number" step="0.01" name="link_length_official" 
                                    class="form-control" 
                                    value="{{ old('link_length_official', $ruas->link_length_official) }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Panjang Ruas (Survei)</label>
                                <input type="number" step="0.01" name="link_length_actual" 
                                    class="form-control" 
                                    value="{{ old('link_length_actual', $ruas->link_length_actual) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Akses ke Jalan</label>
                            <input type="text" name="access_status" 
                                class="form-control" 
                                value="{{ old('access_status', $ruas->access_status) }}">
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('ruas-jalan.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>

                </div>
            </div>
        </div>
    </section>
</div>
@endsection
