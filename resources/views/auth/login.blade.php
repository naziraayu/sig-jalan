<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Login</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-social/bootstrap-social.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
</head>

<body>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                        <div class="login-brand">
                            <img src="{{ asset('assets/img/Logo-Kementerian-Pekerjaan-Umum.jpg') }}" alt="logo" width="100" class="shadow-light">
                        </div>

                        <div class="card card-primary">
                            <div class="card-header"><h4>Login</h4></div>

                            <div class="card-body">
                                <!-- Form Login Laravel -->
                                <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate="">
                                @csrf

                                    <!-- Email -->
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input id="email" type="email" 
                                            class="form-control @error('email') is-invalid @enderror" 
                                            name="email" value="{{ old('email') }}" required autofocus>
                                        @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Password -->
                                    <div class="form-group">
                                        <div class="d-block">
                                        <label for="password" class="control-label">Password</label>
                                        @if (Route::has('password.request'))
                                        <div class="float-right">
                                            <a href="{{ route('password.request') }}" class="text-small">
                                            Forgot Password?
                                            </a>
                                        </div>
                                        @endif
                                        </div>
                                        <input id="password" type="password" 
                                            class="form-control @error('password') is-invalid @enderror" 
                                            name="password" required>
                                        @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Remember Me -->
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="remember" class="custom-control-input" id="remember-me">
                                        <label class="custom-control-label" for="remember-me">Remember Me</label>
                                        </div>
                                    </div>

                                    <!-- Button -->
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                                        Login
                                        </button>
                                    </div>
                                </form>

                                <!-- Optional Social Login -->
                                {{-- <div class="text-center mt-4 mb-3">
                                    <div class="text-job text-muted">Login With Social</div>
                                </div>
                                <div class="row sm-gutters">
                                    <div class="col-6">
                                        <a href="#" class="btn btn-block btn-social btn-facebook">
                                            <span class="fab fa-facebook"></span> Facebook
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="#" class="btn btn-block btn-social btn-twitter">
                                            <span class="fab fa-twitter"></span> Twitter
                                        </a>                                
                                    </div>
                                </div> --}}
                            </div>
                        </div>

                        <div class="mt-5 text-muted text-center">
                            Don't have an account? <a href="{{ route('register') }}">Create One</a>
                        </div>
                        <div class="simple-footer">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/stisla.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
</body>
</html>
