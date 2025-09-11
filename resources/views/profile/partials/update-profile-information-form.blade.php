<form method="post" action="{{ route('profile.update') }}" class="needs-validation" novalidate>
    @csrf
    @method('patch')

    <div class="form-group">
        <label for="name">Nama</label>
        <input 
            id="name" 
            type="text" 
            name="name" 
            value="{{ old('name', $user->name) }}" 
            required 
            autofocus 
            autocomplete="name" 
            class="form-control @error('name') is-invalid @enderror"
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input 
            id="email" 
            type="email" 
            name="email" 
            value="{{ old('email', $user->email) }}" 
            required 
            autocomplete="username" 
            class="form-control @error('email') is-invalid @enderror"
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2">
                <p class="text-muted">
                        Alamat email Anda belum diverifikasi.
                    <button form="send-verification" class="btn btn-link p-0 m-0 align-baseline">
                        Klik di sini untuk mengirim ulang email verifikasi.
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <div class="text-success small">
                        Tautan verifikasi baru telah dikirim ke alamat email Anda.
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="form-group text-right">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        @if (session('status') === 'profile-updated')
            <span class="text-success small ml-2">Simpan.</span>
        @endif
    </div>
</form>
