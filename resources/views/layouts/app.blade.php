<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        <div id="location-status" class="alert alert-info py-2 small d-none" role="status" aria-live="polite">
            <span data-location-message></span>
            <button type="button" class="btn btn-link btn-sm p-0 ms-1" data-location-retry>Coba lagi</button>
        </div>
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

{{-- SCRIPT UNTUK MENGNONAKTIFKAN TOMBOL KALAU LEBIH DARI 500 CHAR --}}
    <script>
// Batas karakter: tampilkan penghitung & nonaktifkan tombol kirim bila melebihi batas.
document.querySelectorAll('[data-char-limit]').forEach(function (field) {
    var max = parseInt(field.dataset.charLimit, 10);
    var form = field.closest('form');
    var button = form.querySelector('[data-char-submit]');
    var counter = form.querySelector('[data-char-counter]');

    function update() {
        // Baris baru dikirim browser sebagai CRLF (2 karakter), sama seperti hitungan di server.
        var len = Array.from(field.value.replace(/\r?\n/g, '\r\n')).length;
        var over = len > max;

        button.disabled = over;
        if (counter) {
            counter.textContent = len + '/' + max;
            counter.hidden = len < max * 0.8;      // muncul saat mendekati batas
            counter.classList.toggle('text-danger', over);
            counter.classList.toggle('fw-bold', over);
        }
    }

    field.addEventListener('input', update);
    field.addEventListener('compositionend', update);
    form.addEventListener('submit', function (event) {
        update();
        if (button.disabled) {
            event.preventDefault();
        }
    });
    update(); // juga saat halaman dimuat (mis. teks lama setelah error validasi)
});
</script>

    @auth
        <script>
            (function () {
                var userId = @json((string) auth()->id());
                var requestKey = 'connectspace-location-requested-' + userId;
                var status = document.getElementById('location-status');
                var message = status.querySelector('[data-location-message]');
                var retryButton = status.querySelector('[data-location-retry]');
                var requestState = sessionStorage.getItem(requestKey);

                function showStatus(text) {
                    message.textContent = text;
                    status.classList.remove('d-none');
                }

                function requestLocation() {
                    sessionStorage.setItem(requestKey, '1');

                    if (!navigator.geolocation) {
                        showStatus('Browser ini tidak mendukung lokasi. Gunakan browser yang mendukung geolokasi.');
                        return;
                    }

                    showStatus('Meminta izin lokasi dari browser...');
                    navigator.geolocation.getCurrentPosition(function (position) {
                        fetch('{{ route('profile.location.update') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude
                            })
                        }).then(function (response) {
                            if (!response.ok) {
                                throw new Error('HTTP ' + response.status);
                            }

                            return response.json();
                        }).then(function (data) {
                            document.querySelectorAll('[data-user-location]').forEach(function (element) {
                                if (element.dataset.userLocation === userId) {
                                    element.textContent = data.location;
                                }
                            });

                            sessionStorage.setItem(requestKey, 'saved');
                            status.classList.add('d-none');
                        }).catch(function (error) {
                            console.error('Gagal menyimpan lokasi perangkat.', error);
                            showStatus('Lokasi didapat, tetapi gagal disimpan. Tekan "Coba lagi" untuk mengulangi.');
                        });
                    }, function (error) {
                        var text = 'Lokasi tidak tersedia. Periksa izin lokasi browser/perangkat, lalu coba lagi.';
                        if (error.code === 1) {
                            text = 'Izin lokasi ditolak. Aktifkan izin lokasi browser/perangkat, lalu coba lagi.';
                        } else if (error.code === 3) {
                            text = 'Permintaan lokasi habis waktu. Tekan "Coba lagi" untuk mengulangi.';
                        }

                        showStatus(text);
                    }, {
                        enableHighAccuracy: false,
                        maximumAge: 600000,
                        timeout: 15000
                    });
                }

                retryButton.addEventListener('click', function () {
                    sessionStorage.removeItem(requestKey);
                    requestLocation();
                });

                if (!requestState) {
                    requestLocation();
                } else if (requestState !== 'saved') {
                    showStatus('Lokasi belum berhasil disimpan. Tekan "Coba lagi" untuk mengulangi.');
                }
            })();
        </script>

        <script>
            (function () {
                var searchButton = document.getElementById('search-post-location');
                if (!searchButton) {
                    return;
                }

                var queryInput = document.getElementById('location-search-query');
                var selectedLocation = document.getElementById('post-location');
                var clearButton = document.getElementById('clear-post-location');
                var searchPanel = document.getElementById('osm-location-search');
                var searchStatus = document.getElementById('location-search-status');
                var results = document.getElementById('location-search-results');

                function showSearchStatus(text, isError) {
                    searchStatus.textContent = text;
                    searchStatus.classList.toggle('text-danger', isError);
                }

                searchButton.addEventListener('click', function () {
                    var query = queryInput.value.trim();
                    if (query.length < 2) {
                        showSearchStatus('Masukkan nama kota minimal 2 karakter.', true);
                        return;
                    }

                    searchButton.disabled = true;
                    results.replaceChildren();
                    showSearchStatus('Mencari kota...', false);

                    fetch(searchButton.dataset.searchUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ query: query })
                    }).then(function (response) {
                        return response.json().then(function (data) {
                            if (!response.ok) {
                                throw new Error(data.message || 'Pencarian kota gagal.');
                            }

                            return data;
                        });
                    }).then(function (data) {
                        if (!data.locations.length) {
                            showSearchStatus('Kota tidak ditemukan. Coba kata kunci lain.', false);
                            return;
                        }

                        showSearchStatus('Pilih kota yang sesuai:', false);
                        data.locations.forEach(function (location) {
                            var option = document.createElement('button');
                            option.type = 'button';
                            option.className = 'list-group-item list-group-item-action';
                            option.setAttribute('role', 'option');
                            option.textContent = location.context
                                ? location.name + ', ' + location.context
                                : location.name;
                            option.addEventListener('click', function () {
                                selectedLocation.value = location.name;
                                clearButton.hidden = false;
                                results.replaceChildren();
                                searchPanel.classList.remove('show');
                                showSearchStatus('Lokasi postingan: ' + location.name, false);
                            });
                            results.appendChild(option);
                        });
                    }).catch(function (error) {
                        console.error('Pencarian kota OpenStreetMap gagal.', error);
                        showSearchStatus(error.message || 'Pencarian kota gagal. Coba lagi nanti.', true);
                    }).finally(function () {
                        searchButton.disabled = false;
                    });
                });

                queryInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        searchButton.click();
                    }
                });

                clearButton.addEventListener('click', function () {
                    selectedLocation.value = '';
                    clearButton.hidden = true;
                    showSearchStatus('', false);
                });
            })();
        </script>
    @endauth

</body>

</html>
