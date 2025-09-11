@extends('layouts.dashboard')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Profile</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Profile</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Halo, {{ auth()->user()->name }}!</h2>
            <p class="section-lead">
                Ubah informasi diri Anda di halaman ini.
            </p>

            <div class="row mt-sm-4">
                <!-- Sidebar Profile Card -->
                <div class="col-12 col-md-12 col-lg-5">
                    <div class="card profile-widget">
                        <div class="profile-widget-header">                     
                            <img alt="image" 
                                src="{{ $user->photo 
                                    ? asset('storage/' . $user->photo) 
                                    : asset('assets/img/avatar/avatar-1.png') }}" 
                                class="rounded-circle profile-widget-picture">
                                <div class="profile-widget-items">
                                    <div class="profile-widget-item">
                                        <div class="profile-widget-item-label">Nama</div>
                                        <div class="profile-widget-item-value">{{ auth()->user()->name }}</div>
                                    </div>
                                    <div class="profile-widget-item">
                                        <div class="profile-widget-item-label">Bergabung</div>
                                        <div class="profile-widget-item-value">{{ auth()->user()->created_at->format('M Y') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="profile-widget-description">
                                <div class="profile-widget-name">{{ auth()->user()->email }}</div>
                                <div class="profile-widget-name">{{ auth()->user()->alamat }}</div>
                                <div class="profile-widget-name">{{ auth()->user()->phone }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Forms -->
                    <div class="col-12 col-md-12 col-lg-7">
                        <!-- Update Profile Photo -->
                        <div class="card">
                            <div class="card-header"><h4>Perbarui Foto Profil</h4></div>
                            <div class="card-body">
                                @include('profile.partials.update-profile-photo-form')
                            </div>
                        </div>

                    <!-- Update Profile Information -->
                    <div class="card">
                        <div class="card-header"><h4>Edit Profil</h4></div>
                        <div class="card-body">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Update Password -->
                    <div class="card">
                        <div class="card-header"><h4>Perbarui Password</h4></div>
                        <div class="card-body">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Delete Account -->
                    <div class="card">
                        <div class="card-header"><h4>Hapus Akun</h4></div>
                        <div class="card-body">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection