<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar · ConnectSpace</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/connectspace.css') }}" rel="stylesheet">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-box">
        <div class="brand mb-1">ConnectSpace</div>
        <p class="meta mb-4">Buat akun dan langsung mulai berbagi.</p>
        @include('partials.alerts')
        <form method="POST" action="{{ route('register.store') }}" class="cs-card p-4" novalidate>
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Nama lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" autofocus
                       class="form-control @error('name') is-invalid @enderror">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Password</label>
                <input type="password" id="password" name="password"
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Minimal 6 karakter.</div>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
            </div>
            <button class="btn btn-brand w-100">Daftar</button>
        </form>
        <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
    </div>
</div>
</body>
</html>
