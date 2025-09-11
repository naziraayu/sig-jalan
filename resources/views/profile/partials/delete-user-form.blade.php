<form method="post" action="{{ route('profile.destroy') }}" class="needs-validation" novalidate
      onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
    @csrf
    @method('delete')

    <div class="alert alert-danger">
        <h5 class="alert-heading">Hapus Akun</h5>
        <p>
            Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen.
            Pastikan Anda telah mengunduh data apa pun yang ingin Anda simpan.
        </p>
    </div>

    <div class="form-group">
        <label for="delete_password">Password</label>
        <input 
            id="delete_password" 
            type="password" 
            name="password" 
            class="form-control @error('password', 'userDeletion') is-invalid @enderror"
            placeholder="Masukkan kata sandi Anda untuk konfirmasi"
        >
        @error('password', 'userDeletion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group text-right">
        <button type="submit" class="btn btn-danger">Hapus Akun</button>
    </div>
</form>
