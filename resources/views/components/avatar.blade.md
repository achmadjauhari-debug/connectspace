Ini **komponen Blade** bernama `avatar`, disimpan di `resources/views/components/avatar.blade.php`. Tugasnya menampilkan foto profil pengguna, dan kalau pengguna belum punya foto, menampilkan lingkaran berisi inisial namanya. Komponen ini dipanggil seperti ini:

`<x-avatar :user="$post->user" :size="44" />`

**1.** `@props(['user', 'size' => 40])`

Baris ini mendeklarasikan nilai yang diterima komponen:

- `@props` menentukan **data apa saja yang boleh dikirim ke komponen**, dan nilai bawaannya kalau tidak dikirim. Ibaratnya daftar parameter pada sebuah fungsi.
- `user` wajib diisi karena tidak punya nilai bawaan. Isinya objek pengguna (model `User`).
- `size` bersifat opsional, dan jika tidak diisi nilainya 40 (piksel).

  Saat komponen dipanggil, atribut yang namanya terdaftar di `@props` menjadi variabel di dalam komponen. Jadi di dalam `avatar.blade.php` Anda bisa langsung memakai `$user` dan `$size`.

```
<x-avatar :user="$post->user" :size="44" />   {{-- $size = 44 --}}
<x-avatar :user="$post->user" />               {{-- $size = 40 (nilai bawaan) --}}

```

**Pembeda dengan atribut biasa.** Atribut yang tidak terdaftar di `@props` tidak menjadi variabel. Atribut itu dikumpulkan di `$attributes` dan otomatis dipasang ke elemen HTML jika Anda menuliskannya:

```
<img src="..." {{ $attributes }}>
```

Contohnya, `<x-avatar :user="$u" class="shadow" />` akan menyertakan `class="shadow"` di `<img>` tersebut. Komponen avatar kita tidak memakai `{{ $attributes }}`, jadi atribut tambahan seperti itu diabaikan.

**Kalau prop wajib tidak dikirim.** Pada `<x-avatar />` tanpa `:user`, variabel `$user` tidak ada, dan Laravel menampilkan error `Undefined variable $user`. Ini sama seperti error `$comment` yang Anda alami sebelumnya, yaitu variabel yang dipakai tapi tidak pernah diberikan.

**Tanda titik dua.** `:user="$post->user"` dan `:size="44"` memakai titik dua karena nilainya adalah ekspresi PHP (objek atau angka). Tanpa titik dua, nilainya dibaca sebagai teks biasa, sehingga `size="44"` menjadi string `"44"` dan `user="$post->user"` menjadi teks harfiah `$post->user`.

Komponen `x-logo` juga memakai `@props`: `@props(['size' => 36, 'dark' => false, 'tagline' => false])`. Di sana ketiganya opsional, jadi `<x-logo />` tanpa atribut apa pun tetap berjalan dengan nilai bawaan.

**2.** `@if ($user->avatar)`

Mengecek kolom `avatar` di tabel `users`. Kolom itu berisi path berkas hasil upload atau URL gambar, dan bernilai `NULL` jika belum ada foto.

**3. Cabang jika ada foto: tag** `<img>`

- `src="{{ $user->avatar_src }}"` memakai accessor `getAvatarSrcAttribute()` di model `User`. Accessor itu mengubah path upload menjadi alamat lengkap (`asset('storage/...')`), sedangkan URL eksternal dibiarkan apa adanya.
- `alt="Foto profil {{ $user->name }}"` adalah teks pengganti untuk pembaca layar atau saat gambar gagal dimuat.
- `class="avatar"` memberi bentuk bulat dan `object-fit: cover` dari `connectspace.css`, supaya foto tidak gepeng.
- `style="width:...px;height:...px"` mengatur ukuran sesuai `$size`.
- `loading="lazy"` membuat browser menunda memuat foto yang belum terlihat di layar, sehingga halaman feed yang panjang lebih ringan.

**4. Cabang jika tidak ada foto: tag** `<span>`

- `{{ $user->initials }}` memakai accessor `getInitialsAttribute()`, yaitu huruf pertama dari dua kata pertama nama dalam huruf kapital. “Rani Pratama” menjadi **RP**.
- `class="avatar avatar-fallback"` membuatnya berbentuk lingkaran teal dengan tulisan putih.
- `font-size:{{ round($size * 0.4) }}px` membuat ukuran huruf selalu 40% dari ukuran lingkaran, jadi inisial tetap proporsional di semua ukuran. Ukuran 44 menghasilkan huruf 18px, dan ukuran 72 menghasilkan 29px.
- `aria-hidden="true"` menyembunyikan inisial dari pembaca layar. Inisial itu hanya hiasan, dan nama pengguna biasanya sudah tertulis di sebelahnya.

**5.** `@endif`

Menutup percabangan.

Semua nilai ditulis dengan `{{ }}`, yang otomatis meng-escape karakter HTML. Jadi nama seperti `<script>` tidak akan dieksekusi sebagai kode.

Komponen ini dipakai di beberapa tempat dengan ukuran berbeda: 34 di navbar dan komentar, 44 di postingan dan form status, serta 72 di halaman profil. Karena semua memakai komponen yang sama, mengubah tampilan avatar cukup dilakukan di satu file ini.

Ada satu keterbatasan. Komponen hanya mengecek apakah kolom `avatar` terisi, bukan apakah gambarnya benar-benar bisa dibuka. Kalau pengguna memasukkan URL foto yang rusak atau sudah dihapus, yang tampil adalah ikon gambar rusak, bukan inisial. Kalau mau, saya bisa menambahkan cadangan otomatis ke inisial untuk kasus itu.