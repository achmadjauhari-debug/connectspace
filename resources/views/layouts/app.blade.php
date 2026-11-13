<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ConnectSpace Project 2026')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/connectspace.css') }}" rel="stylesheet">
</head>

<body>
    @auth
        <nav class="cs-nav py-2">
            <div class="container d-flex justify-content-between align-items-center" style="max-width:720px">
                {{-- <a href="{{ route('feed') }}" class="brand">ConnectSpace Project </a> --}}
                <a href="{{ route('feed') }}" class="text-decoration-none">
                    <x-logo :size="38" :dark="true" :tagline="true" />
                </a>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('profile.edit') }}" class="nav-user">
                        <x-avatar :user="auth()->user()" :size="34" />
                        <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}"
                        onsubmit="konfirmasiLogout(event, 'Yakin ingin keluar dari ConnectSpace?')">
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function konfirmasi(event, pesan, opsi) {
            event.preventDefault();
            const form = event.target;

            // Cadangan jika SweetAlert2 gagal dimuat
            if (typeof Swal === 'undefined') {
                if (window.confirm(pesan)) form.submit();
                return;
            }

            Swal.fire({
                title: 'ConnectSpace',
                text: pesan,
                icon: opsi.icon,
                showCancelButton: true,
                confirmButtonColor: opsi.warna,
                cancelButtonColor: '#6c757d',
                confirmButtonText: opsi.tombol,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        }

        function konfirmasiHapus(event, pesan) {
            konfirmasi(event, pesan, {
                icon: 'warning',
                warna: '#d33',
                tombol: 'Ya, Hapus'
            });
        }

        function konfirmasiLogout(event, pesan) {
            konfirmasi(event, pesan, {
                icon: 'question',
                warna: '#0b6e75',
                tombol: 'Ya, Keluar'
            });
        }
    </script>


</body>

</html>
