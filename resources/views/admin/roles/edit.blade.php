@extends('layouts.dashboard')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Hak Akses</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </div>
                <div class="breadcrumb-item">
                    <a href="{{ route('roles.index') }}">Manajemen Hak Akses</a>
                </div>
                <div class="breadcrumb-item">Edit Hak Akses</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Form Edit Hak Akses</h2>
            <p class="section-lead">Gunakan form ini untuk mengubah nama role dan mengatur permissions yang terkait.</p>

            <form action="{{ route('roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nama Hak Akses</label>
                            <input type="text" 
                                    name="name" 
                                    id="name" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    value="{{ old('name', $role->name) }}" 
                                    required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Permissions</label>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                        <th>Fitur</th>
                                        @php
                                            // Ambil semua jenis aksi yang ada
                                            $allActions = collect($permissionsGrouped)->flatten()->pluck('action')->unique();
                                        @endphp
                                        @foreach($allActions as $action)
                                            <th class="text-center">{{ ucfirst($action) }}</th>
                                        @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($permissionsGrouped as $feature => $permissions)
                                        <tr>
                                            <td>{{ ucfirst(str_replace('_', ' ', $feature)) }}</td>
                                            @foreach($allActions as $action)
                                            @php
                                                $perm = $permissions->firstWhere('action', $action);
                                            @endphp
                                            <td class="text-center">
                                                @if($perm)
                                                <input type="checkbox" 
                                                        name="permissions[]" 
                                                        value="{{ $perm->id }}" 
                                                        id="permission_{{ $perm->id }}"
                                                        {{ in_array($perm->id, $rolePermissions) ? 'checked' : '' }}>
                                                @endif
                                            </td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
