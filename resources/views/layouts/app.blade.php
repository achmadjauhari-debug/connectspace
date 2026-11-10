<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ConnectSpace')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/connectspace.css') }}" rel="stylesheet">
</head>
<body>
@auth
    <nav class="cs-nav py-2">
        <div class="container d-flex justify-content-between align-items-center" style="max-width:720px">
            <a href="{{ route('feed') }}" class="brand">ConnectSpace</a>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('profile.edit') }}" class="nav-user">
                    <x-avatar :user="auth()->user()" :size="34" />
                    <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-light">Keluar</button>
                </form>
            </div>
        </div>
    </nav>
@endauth

<main class="container py-4" style="max-width:720px">
    @include('partials.alerts')
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
