@foreach (['success' => 'success', 'warning' => 'warning', 'error' => 'danger'] as $key => $type)
    @if (session($key))
    {{-- session($key) ini diambil dari AuthController dit ->with(success.. dan akan menghasilkan session(success)) --}}


    <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
        {{-- $type jadi alert-success = hijau, alert-warning = kuning, alert-danger = merah --}}
   
        {{-- Mencetak teks pesan yang dikirimkan dari AuthController ke dalam kotak notifikasi. --}}
        
            {{ session($key) }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
@endforeach
