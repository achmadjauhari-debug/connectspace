Penjelasan &lt;x-logo&gt;

`<x-logo />` adalah **komponen Blade**. Awalan `x-` memberi tahu Laravel untuk mencari file view di `resources/views/components/`, dan nama setelahnya menunjuk ke file `logo.blade.php` di folder itu. Jadi kode di atas membaca `resources/views/components/logo.blade.php`.

**Logo tidak diambil dari file gambar.** Ikon dan teksnya digambar langsung di dalam file `logo.blade.php`:

- ikon "C" berupa kode `<svg>` yang tertulis di dalam komponen itu;
- tulisan "ConnectSpace" dan "PPKD JP 2026" berupa teks HTML biasa.

Karena itu file di `public/images/` (`logo-icon.svg`, `logo-full.svg`, `logo-full-dark.svg`) tidak dibaca oleh `<x-logo>`. File-file itu dipakai untuk keperluan lain, misalnya favicon lewat `<link rel="icon">`, atau diunduh untuk poster dan dokumen. Kalau Anda mengubah ikon di `logo.blade.php`, file di `public/images/` tidak ikut berubah, begitu juga sebaliknya.

Atribut yang dikirim ke komponen:

| Atribut | Contoh | Pengaruh |
| --- | --- | --- |
| `:size` | `:size="38"` | ukuran ikon dalam piksel, dan ukuran teks menyesuaikan |
| `:dark` | `:dark="true"` | teks putih, untuk latar gelap seperti navbar |
| `:tagline` | `:tagline="true"` | menampilkan "PPKD JP 2026" di bawah nama |

Tanda titik dua (`:size`, `:dark`, `:tagline`) berarti nilainya dibaca sebagai ekspresi PHP, yaitu angka atau `true`/`false`. Tanpa titik dua, nilainya dibaca sebagai teks biasa, sehingga `dark="false"` justru dianggap benar.

`<a href="{{ route('feed') }}">` yang membungkusnya hanya membuat logo bisa diklik menuju halaman Beranda. `class="text-decoration-none"` menghilangkan garis bawah tautan.

Kalau mau memakai file gambar sebagai gantinya, ganti dengan `<img src="{{ asset('images/logo-full-dark.svg') }}" height="38" alt="ConnectSpace">`. Kekurangannya, teks di dalam SVG yang dimuat lewat `<img>` tidak memakai font situs, jadi tampilannya bisa sedikit berbeda.