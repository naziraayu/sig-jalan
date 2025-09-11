@extends('layouts.template')

@section('content')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Users</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active">
          <a href="{{ route('dashboard') }}">Dashboard</a>
        </div>
        <div class="breadcrumb-item">Manajemen User</div>
      </div>
    </div>

    <div class="section-body">
      <h2 class="section-title">Data User</h2>
      <p class="section-lead">Daftar semua user yang terdaftar di sistem.</p>

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4>Tabel User</h4>
          <a href="{{ route('users.create') }}" class="btn btn-primary">+ Tambah User</a>
        </div>
        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif

          <div class="table-responsive">
            <table id="userTable" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>Role</th>
                  {{-- <th>Alamat</th>
                  <th>No. Telp</th> --}}
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($users as $user)
                  <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role ? $user->role->name : '-' }}</td>
                    {{-- <td>{{ $user->alamat ?? '-' }}</td>
                    <td>{{ $user->phone ?? '-' }}</td> --}}
                    <td>
                      <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i>
                      </a>
                      <form action="{{ route('users.destroy', $user->id) }}" 
                            method="POST" 
                            class="d-inline" 
                            onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    $('#userTable').DataTable({
      paging: true,
      searching: true,
      ordering: true,
      info: true,
      lengthChange: true,
      pageLength: 10,
    });
  });
</script>
@endpush
