```php
    public static function store(Request $request, string $fileField, string $urlField, string $dir): ?string
    {
        if ($request->hasFile($fileField)) {
            // hashName() + ekstensi dari MIME hasil deteksi -> nama file aman
            return $request->file($fileField)->store($dir, 'public');
        }

        $url = $request->input($urlField);

        return $url ? (string) $url : null;
    }
```

Kode tersebut adalah kelas bantuan kustom (Helper Class) bernama ImageStorage yang berada di folder <span style="color: rgb(232, 201, 74);">app/Support/ImageStorage.php.</span> 

Fungsi utamanya adalah mengelola penyimpanan, penghapusan, dan penampilan URL gambar secara fleksibel, baik yang berasal dari file unggahan lokal (upload) maupun dari tautan internet eksternal (URL). Semua metodenya dibuat static sehingga bisa langsung dipanggil dari mana saja (seperti dari Controller atau Model) tanpa perlu membuat objek baru.

 <span style="color: rgb(232, 201, 74);">Fungsi:</span> Menerima input gambar dari form dan mengembalikan lokasi/path gambar untuk disimpan ke database. 

• Prioritas File Upload: Mengecek apakah pengguna mengunggah file melalui $request->hasFile($fileField). Jika ada, file disimpan ke folder tujuan ($dir) di dalam penyimpanan storage/app/public dengan nama acak yang aman (mencegah nama file bentrok atau celah keamanan upload). 

• Alternatif URL Eksternal: Jika tidak ada file yang diunggah, sistem mengecek apakah pengguna mengisi kolom tautan gambar ($urlField). Jika ada, teks URL tersebut langsung dikembalikan; jika kosong keduanya, mengembalikan null.

Dua parameter di dalam metode `->store($dir, 'public')` inilah yang menentukan lokasi penyimpanannya:

- <span>**Parameter ke-2 (**</span>`'public'`<span>**):** Memberi tahu Laravel untuk menggunakan disk penyimpanan bernama </span>`public`<span> (yang dikonfigurasi di file </span>`config/filesystems.php`<span>), yaitu mengarah ke direktori fisik </span>`storage/app/public/`<span>.</span>
- <span>**Parameter ke-1 (**</span>`$dir`<span>**):** Menentukan nama sub-folder di dalamnya. Saat Anda memanggil metode ini dari Controller, misalnya:</span>

```php
 $newAvatar = ImageStorage::store($request, 'avatar', 'avatar_url', 'avatars');
```

- <span>Nilai </span>`'avatars'`<span> masuk ke dalam variabel </span>`$dir`<span>.</span>
- <span>**Pembuatan folder otomatis:** Jika folder </span>`storage/app/public/avatars`<span> belum ada, metode </span>`->store()`<span> milik Laravel **secara otomatis membuatkan folder tersebut** terlebih dahulu sebelum meletakkan file gambar di dalamnya.</span>

**2. Mengapa Gambar Bisa Muncul di** `public/storage/avatar`**?**

Secara fisik, file gambar sebenarnya disimpan di `storage/app/public/avatars/namafile.jpg`, bukan di dalam folder `public` utama. Namun, browser pengguna tidak bisa mengakses folder `storage` secara langsung demi alasan keamanan.

Agar gambar bisa diakses lewat browser di alamat `public/storage/avatars/namafile.jpg`, proyek Anda menggunakan perintah terminal bawaan Laravel:

php artisan storage:link

- <span>**Cara kerja**</span> `storage:link`<span>**:** Perintah ini membuat **jalan pintas (*symbolic link*)** bernama </span>`public/storage`<span> yang langsung tembus/tercermin ke folder </span>`storage/app/public`<span>.</span>
- <span>**Hasil akhirnya:** Apa pun folder yang otomatis dibuat oleh Laravel di dalam </span>`storage/app/public/`<span> (seperti </span>`avatars`<span> atau </span>`posts`<span>), otomatis akan langsung muncul dan bisa diakses melalui </span>`public/storage/`<span>.</span>

```php
  public static function isUrl(?string $value): bool
    {
        return (bool) ($value && preg_match('#^https?://#i', $value));
    }
```

- <span>**Fungsi:** Memeriksa apakah nilai string yang tersimpan di database merupakan tautan web eksternal atau path file lokal.</span>
- <span>**Cara kerja:** Menggunakan *Regular Expression* (</span>`preg_match('#^https?://#i', $value)`<span>) untuk mengecek apakah teks diawali dengan </span>`http://`<span> atau </span>`https://`<span> (tidak peka huruf besar/kecil).</span>

```php
    public static function delete(?string $value): void
    {
        if ($value && ! self::isUrl($value)) {
            Storage::disk('public')->delete($value);
        }
    }
```

- <span>**Fungsi:** Menghapus file gambar dari server (misalnya saat pengguna mengganti foto profil atau menghapus postingan).</span>
- <span>**Perlindungan URL:** Sebelum menghapus, kode memeriksa </span>`if ($value && ! self::isUrl($value))`<span>. Artinya, penghapusan di disk </span>`public`<span> **hanya dijalankan jika gambar tersebut adalah file lokal** di server Anda, bukan tautan eksternal.</span>

```php
    public static function url(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return self::isUrl($value) ? $value : asset('storage/' . $value);
    }
```

- <span>**Fungsi:** Menghasilkan alamat lengkap yang bisa langsung dimasukkan ke dalam atribut HTML </span>`<img src="...">`<span> (inilah yang dipanggil oleh </span>`$user->avatar_src`<span> pada Model </span>`User`<span> Anda sebelumnya).</span>
- <span>**Alur penentuan alamat:**</span>
  - <span>Jika </span>`$value`<span> kosong (</span>`null`<span>), kembalikan </span>`null`<span>.</span>
  - <span>Jika </span>`$value`<span> adalah URL internet (</span>`https://...`<span>), langsung kembalikan URL tersebut apa adanya.</span>
  - <span>Jika </span>`$value`<span> adalah path file lokal (misalnya </span>`avatars/foto123.jpg`<span>), gabungkan dengan helper </span>`asset('storage/' . $value)`<span> sehingga menjadi </span>`http://websiteanda.com`<span>.</span>