<form method="POST" action="{{ route('profile.update.photo') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="photo">Foto Profil</label>
        <input id="photo" type="file" class="form-control @error('photo') is-invalid @enderror" name="photo">
        @error('photo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Perbarui Foto</button>
</form>
