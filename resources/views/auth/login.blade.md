```blade
[CSS CUSTOM connectspace.css]
<div class="auth-wrap">
.auth-wrap {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
}
Pembungkus Luar / Halaman Penuh
• Peran: Bertindak sebagai wadah utama yang membungkus keseluruhan layar halaman login/register.
• Gaya CSS yang umum digunakan: Biasanya diatur agar memiliki tinggi penuh satu layar (min-height: 100vh), menggunakan Flexbox atau Grid (display: flex; 
  justify-content: center; align-items: center;) untuk memposisikan kotak formulir tepat di tengah-tengah layar, serta memberikan warna latar belakang 
  atau jarak tepi (padding).
###
<div class="auth-box">
.auth-box {
    width: 100%;
    max-width: 420px;
}
<div class="brand mb-1">ConnectSpace Project 2026</div>
.auth-box .brand {
    font-weight: 800;
    font-size: 1.8rem;
    letter-spacing: -0.02em;
}

<p class="meta mb-4">Masukan kredensial anda untuk ke beranda</p>
.meta {
    color: var(--muted);
    font-size: 0.85rem;
}

############################################################################################################################################
[CODE]
<form method="POST" action="{{ route('login.attempt') }}" class="cs-card p-4" novalidate>
• method="POST"
  Metode pengiriman rahasia. Memastikan data seperti email dan kata sandi dikirim di dalam tubuh permintaan HTTP (request body), 
  bukan ditempelkan di alamat URL browser. Ini wajib digunakan untuk form login dan wajib dipasangkan dengan token @csrf di dalam form.
• action="{{ route('login.attempt') }}"
  Tujuan pengiriman data. Mengarahkan data form menuju rute bernama login.attempt (yaitu URL /login yang terhubung ke metode login() di AuthController).
• class="cs-card p-4"
  Pengaturan gaya tampilan (CSS). Menggabungkan kelas kustom proyek Anda (cs-card, kemungkinan singkatan dari ConnectSpace Card) 
  dengan kelas bawaan Bootstrap 5 (p-4, yang memberikan jarak dalam atau padding di keempat sisi kotak sebesar 1.5rem).
.cs-card {
    background: var(--paper);
    border: 1px solid var(--line);
    border-radius: 14px;
    }
• novalidate
  Mematikan pop-up validasi bawaan browser. Atribut HTML5 ini mencegah browser memunculkan balon peringatan standar seperti "Please fill out this field" 
  dalam bahasa Inggris/bawaan sistem.
  Alasan penggunaannya: Agar seluruh pemeriksaan input sepenuhnya ditangani oleh validasi server Laravel ($request->validate(...) dan kelas Pesan) 
  sehingga tampilan pesan kesalahannya seragam, berbahasa Indonesia, dan muncul rapi di bawah kolom input.

[CODE]
<label for="email" class="form-label fw-semibold">Email</label>
Baris kode tersebut adalah elemen label HTML (<label>) untuk kolom input email yang menggunakan kelas utilitas bawaan Bootstrap 5.

• for="email"
  Menghubungkan label dengan kolom input. Nilai "email" ini harus sama persis dengan atribut id="email" pada tag <input> di bawahnya.
  Manfaatnya: Ketika pengguna mengklik teks "Email", kursor ketik otomatis aktif masuk ke dalam kotak input email. 
  Ini juga sangat membantu pembaca layar (screen reader) untuk aksesibilitas.
• class="form-label fw-semibold"
  form-label: Kelas bawaan Bootstrap 5 untuk mengatur tata letak dan jarak bawah (margin-bottom) label formulir agar rapi saat berada tepat di atas kotak input.
  fw-semibold: Singkatan dari Font Weight Semibold (kelas utilitas Bootstrap 5) yang membuat teks "Email" tercetak setengah tebal agar lebih jelas dibaca dan menonjol dibanding teks biasa.
• Email
  Teks judul kolom yang dilihat langsung oleh pengguna di layar.

[CODE]
<input type="email" id="email" name="email" value="{{ old('email') }}" autofocus class="form-control @error('email') is-invalid @enderror">

1. Rincian Atribut pada Tag <input>
• type="email": Menentukan jenis kotak input untuk alamat email (di ponsel, ini otomatis memunculkan tombol @ pada keyboard).
• id="email": Identitas elemen HTML yang terhubung dengan <label for="email"> di atasnya.
• name="email": Kunci nama data yang dikirim ke server. Nama inilah yang dibaca oleh $request->input('email') dan aturan validasi 'email' => [...] di AuthController.
• value="{{ old('email') }}": Mengisi kembali kotak input dengan teks email yang diketik 
  pengguna sebelumnya jika login/validasi gagal (berasal dari perintah ->onlyInput('email') di AuthController), sehingga pengguna tidak perlu mengetik ulang.
• autofocus: Otomatis menempatkan kursor ketik di kolom ini saat halaman pertama kali dibuka agar pengguna bisa langsung mengetik tanpa perlu mengklik kotak input.
[CODE]
class="form-control @error('email') is-invalid @enderror":
• form-control: Kelas standar Bootstrap 5 agar kotak input tampil modern dan lebar penuh.
• @error('email') is-invalid @enderror: Direktif Blade yang mengecek apakah ada error validasi untuk kolom email. 
  Jika ada error, Bootstrap otomatis menambahkan kelas is-invalid yang membuat bingkai kotak input berubah menjadi merah disertai ikon tanda seru.

2. Rincian Penampil Pesan Error (@error)

@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror

• @error('email') ... @enderror: Blok kondisional khusus di Laravel Blade. 
  Isi di dalamnya hanya akan muncul di layar jika validasi untuk email gagal (misalnya email kosong, format salah, 
  atau "Email atau password salah." dari withErrors).
• class="invalid-feedback": Kelas bawaan Bootstrap 5 yang membuat teks error tampil berwarna merah kecil tepat di bawah kotak input. 
  Kelas ini secara alami bekerja berpasangan dengan kelas is-invalid pada <input> di atasnya.
• {{ $message }}: Variabel khusus bawaan direktif @error yang otomatis mencetak kalimat pesan kesalahan yang dikirimkan oleh AuthController atau kelas Pesan.

Alur pengiriman pesan dari AuthController hingga menjadi variabel {{ $message }} di Blade terjadi secara otomatis 
melalui mekanisme Session dan View Composer bawaan Laravel.
Saat terjadi kegagalan di AuthController, Laravel membungkus semua pesan kesalahan ke dalam objek khusus bernama $errors (ViewErrorBag), 
lalu direktif @error('email') membongkarnya untuk Anda menjadi variabel $message.

Rincian Alur Perjalanan Data dari AuthController ke Blade

1. Pemicu Error di AuthController:
Di dalam AuthController, ada dua tempat yang menciptakan pesan kesalahan untuk kunci 'email':
• Saat gagal validasi ($request->validate(...)): Jika email kosong atau formatnya salah, Laravel mengambil teks dari Pesan::messages() 
  dan menyimpannya di bawah kunci 'email'.
• Saat gagal mencocokkan akun (return back()->withErrors(...)): Anda menulis secara manual:
php
return back()->withErrors(['email' => 'Email atau password salah.']);
Kunci array 'email' di sinilah yang menjadi jembatan penghubungnya.
2. Penyimpanan Sementara di Session:
Sebelum mengembalikan pengguna ke halaman login (back()), Laravel menyimpan daftar kesalahan tersebut ke dalam flash session bernama errors.
3. Pembagian Otomatis ke Semua Halaman Blade (Middleware):
Saat halaman login dimuat ulang, middleware bawaan Laravel bernama ShareErrorsFromSession otomatis berjalan. 
Middleware ini mengambil data error dari session tadi dan membagikannya ke seluruh file Blade dalam bentuk variabel global bernama $errors.

Kesimpulan Kuncinya
• Nama kunci harus sama persis: Kunci 'email' di dalam $request->validate(['email' => ...]) dan withErrors(['email' => ...]) 
  pada AuthController harus sama persis dengan parameter 'email' di dalam @error('email') pada file Blade.
• Variabel $message bersifat lokal: Variabel $message tidak pernah dibuat di AuthController, melainkan otomatis diciptakan oleh direktif @error(...) 
  di dalam Blade untuk menampung pesan error pertama milik kunci tersebut.

```