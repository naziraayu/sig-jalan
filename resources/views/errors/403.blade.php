@extends('layouts.template') {{-- kalau pakai layout utama --}}

@section('content')
<div id="app">
    <section class="section">
        <div class="container mt-5">
            <div class="page-error">
                <div class="page-inner">
                    <h1>403</h1>
                    <div class="page-description">
                        Kamu tidak memiliki akses untuk membuka halaman ini.
                    </div>
                    <div class="page-search">
                        <div class="mt-3">
                            <a href="{{ route('dashboard') }}">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="simple-footer mt-5">
                Copyright &copy;
            </div>
        </div>
    </section>
</div>
@endsection
