<form method="post" action="{{ route('password.update') }}" class="needs-validation" novalidate>
    @csrf
    @method('put')

    <div class="form-group">
        <label for="current_password">Kata Sandi Saat Ini</label>
        <input 
            id="current_password" 
            type="password" 
            name="current_password" 
            autocomplete="current-password"
            class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
        >
        @error('current_password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">Kata Sandi Baru</label>
        <input 
            id="password" 
            type="password" 
            name="password" 
            autocomplete="new-password"
            class="form-control @error('password', 'updatePassword') is-invalid @enderror"
        >
        @error('password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation">Konfirmasi Kata Sandi</label>
        <input 
            id="password_confirmation" 
            type="password" 
            name="password_confirmation" 
            autocomplete="new-password"
            class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
        >
        @error('password_confirmation', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group text-right">
        <button type="submit" class="btn btn-primary">Perbarui Kata Sandi</button>
        @if (session('status') === 'password-updated')
            <span class="text-success small ml-2">Simpan.</span>
        @endif
    </div>
</form>
