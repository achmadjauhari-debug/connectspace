# ConnectSpace — MVP Media Sosial Internal (Laravel + MySQL)

Implementasi soal UJK Junior Web Programmer. Isi folder ini adalah **file aplikasi saja**
(overlay) yang ditimpa ke proyek Laravel baru (kompatibel Laravel 10 / 11 / 12, PHP 8.1+).

## Instalasi (XAMPP / Windows)

1. Nyalakan **Apache** dan **MySQL** di XAMPP, lalu buat database kosong `connectspace`
   lewat phpMyAdmin (collation `utf8mb4_unicode_ci`).
2. Buat proyek Laravel baru:
   ```
   composer create-project laravel/laravel connectspace
   cd connectspace
   ```
3. Salin **seluruh isi** folder ini ke dalam folder proyek `connectspace` (pilih *Replace/Overwrite*).
4. Hapus migration bawaan Laravel untuk tabel users (diganti migration milik ConnectSpace):
   - Laravel 11/12: `database\migrations\0001_01_01_000000_create_users_table.php`
   - Laravel 10: `database\migrations\2014_10_12_000000_create_users_table.php`
     dan `2014_10_12_100000_create_password_reset_tokens_table.php`
5. Ubah `.env`:
   ```
   APP_NAME=ConnectSpace
   APP_TIMEZONE=Asia/Jakarta
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=connectspace
   DB_USERNAME=root
   DB_PASSWORD=

   SESSION_DRIVER=file
   CACHE_STORE=file      # Laravel 10: CACHE_DRIVER=file
   ```
   (Laravel 10: `APP_TIMEZONE` diatur di `config/app.php` → `'timezone' => 'Asia/Jakarta'`.)
6. Jalankan:
   ```
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
   ```
7. Buka http://127.0.0.1:8000 — akun demo (seeder): `rani@connectspace.test` / `password`
   dan `budi@connectspace.test` / `password`. Anda juga bisa mendaftar akun baru.

> Jika `storage:link` gagal di Windows, jalankan terminal sebagai Administrator.
> Upload gambar maksimal 2 MB (sesuai `upload_max_filesize` bawaan XAMPP).

## Lokasi pada postingan

Pemilik postingan dapat memilih nama kota melalui pencarian OpenStreetMap. Pencarian hanya
dikirim saat tombol ditekan; aplikasi menyimpan nama kota, bukan koordinat. Hasil ditampilkan
dengan atribusi OpenStreetMap. Pencarian dicache selama 30 hari dan dibatasi satu permintaan
per detik secara global agar sesuai kebijakan layanan publik Nominatim.

URL Nominatim dan identitas aplikasi dapat diganti tanpa perubahan kode melalui
`OPENSTREETMAP_NOMINATIM_URL` dan `OPENSTREETMAP_USER_AGENT`. Pastikan cache aplikasi
dibagikan antar proses/server jika aplikasi dijalankan pada beberapa server.
Jika verifikasi TLS PHP tidak menemukan CA bundle, atur `OPENSTREETMAP_CA_BUNDLE`;
XAMPP Windows otomatis memakai `apache\bin\curl-ca-bundle.crt` jika tersedia.

## Pemetaan ke soal

| Ketentuan soal | Implementasi |
|---|---|
| 4 tabel + FK + unique likes | `database/migrations/*` (`likes` UNIQUE `user_id, post_id`, semua FK `ON DELETE CASCADE`) |
| Registrasi + validasi + **auto-login** | `AuthController@register` → `Auth::login()` → redirect ke Beranda |
| Password ter-hash | `Hash::make()` (bcrypt), login via `Auth::attempt()` |
| Proteksi rute (tamu → Login + peringatan) | middleware `EnsureAuthenticated` |
| Login/Register dilarang bila sudah login | middleware `RedirectIfLoggedIn` |
| Logout menghancurkan sesi | `AuthController@logout` (invalidate + regenerate token) |
| Ubah profil + foto (upload / URL) | `ProfileController@update` |
| Ubah password, verifikasi password lama | `ProfileController@updatePassword` |
| Avatar di navbar, postingan, komentar | komponen `<x-avatar>` (fallback inisial bila belum ada foto) |
| Status teks/gambar, terbaru di atas | `FeedController@store/index` (`ORDER BY created_at DESC`) |
| Like toggle, 1 user 1 like | `LikeController@toggle` + UNIQUE constraint |
| Komentar teks + gambar | `CommentController@store` |
| Hapus hanya oleh pemilik | tombol hanya dirender untuk pemilik + `abort_unless` (403) di server |
| Keamanan | Eloquent/Query Builder (prepared statement), CSRF, validasi MIME gambar, `{{ }}` escape XSS, nama file di-hash |
