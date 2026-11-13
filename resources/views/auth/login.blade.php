<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · ConnectSpace</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/connectspace.css') }}" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-icon.svg') }}">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-box">
        <div class="d-flex mb-3 justify-content-center"><x-logo :size="52" :tagline="true" /></div>
        <p class="meta mb-4 text-center">Masukan kredensial anda untuk ke beranda</p>
        @include('partials.alerts')
        <form method="POST" action="{{ route('login.attempt') }}" class="cs-card p-4" novalidate>
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Password</label>
                <input type="password" id="password" name="password"
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
            <button class="btn btn-brand w-100">Masuk</button>
        </form>
        <p class="text-center mt-3 mb-0">Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
    </div>
</div>
</body>
</html>
