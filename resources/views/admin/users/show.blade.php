@extends('layouts.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Detail User</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </div>
                <div class="breadcrumb-item">
                    <a href="{{ route('users.index') }}">User Management</a>
                </div>
                <div class="breadcrumb-item">Detail User</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Profil {{ $user->name }}</h2>
            <p class="section-lead">Detail informasi user yang dipilih.</p>

            <div class="card">
                <div class="card-body">
                    <p><strong>Foto:</strong></p>
                    @if($user->photo)
                    <img src="{{ asset('storage/' . $user->photo) }}" class="img-thumbnail" width="150">
                    @else
                    <span class="text-muted">Belum ada foto</span>
                    @endif

                    <p><strong>Nama:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Role:</strong> {{ $user->role ? $user->role->name : '-' }}</p>
                    <p><strong>Alamat:</strong> {{ $user->alamat ?? '-' }}</p>
                    <p><strong>No. Telp:</strong> {{ $user->phone ?? '-' }}</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
