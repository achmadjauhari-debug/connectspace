## proses registrasi

```php
use App\Models\User;
MODEL : User.php
use Illuminate\Foundation\Auth\User as Authenticatable;
• Illuminate\Foundation\Auth\User: Ini adalah kelas inti bawaan Laravel yang sudah dilengkapi berbagai fitur keamanan login (seperti mengelola password, 
  mengecek sesi pengguna, dan mengatur kolom remember_token).
• as Authenticatable: Kata kunci as di PHP digunakan untuk membuat nama alias (nama samaran). 
  Jadi, di dalam file tersebut, kelas bawaan Laravel tadi akan dipanggil dengan nama Authenticatable.

Mengapa Harus Diberi Nama Alias?
Model buatan Anda sendiri juga bernama User. Jika baris di atas ditulis tanpa alias (use Illuminate\Foundation\Auth\User;), 
PHP akan mengalami kebingungan dan memunculkan error (Cannot declare class User, because the name is already in use) karena ada dua kelas 
bernama User di dalam satu file yang sama.

Fungsi Utama Setelah Mewarisi Kelas Ini

Dengan menulis class User extends Authenticatable, model User Anda mendapatkan dua kekuatan sekaligus:
• Kemampuan Model Eloquent biasa: Bisa melakukan query database seperti User::create(), User::find(), dan membuat relasi hasMany().
• Kemampuan Autentikasi Laravel: Bisa langsung dimasukkan ke dalam fungsi Auth::login($user) atau Auth::attempt() tanpa perlu konfigurasi rumit.

[CODE]
protected $fillable = ['name', 'email', 'password', 'avatar', 'bio'];
• protected $fillable = [...]: Daftar kolom (name, email, password, avatar, bio) yang diizinkan untuk diisi secara massal (mass assignment), 
misalnya saat memanggil perintah User::create([...]) di AuthController. Kolom di luar daftar ini akan ditolak demi keamanan.

[CODE]
protected $hidden = ['password', 'remember_token'];
protected $hidden = [...]: Menyembunyikan kolom password dan remember_token secara otomatis setiap kali data pengguna 
diubah menjadi format array atau JSON (misalnya untuk API), sehingga data rahasia tidak bocor ke publik.

[CODE]
{return $this->hasMany(Post::class)}
{return $this->hasMany(Comment::class)}
{return $this->hasMany(Like::class)}
2. Relasi Database (One-to-Many)
Ketiga metode ini menghubungkan satu pengguna dengan banyak data lain menggunakan relasi HasMany:
• posts(): Menghubungkan pengguna dengan semua postingan yang ia buat ($user->posts).
• comments(): Menghubungkan pengguna dengan semua komentar yang pernah ia tulis ($user->comments).
• likes(): Menghubungkan pengguna dengan semua tanda suka (like) yang ia berikan ($user->likes).

3. Atribut Virtual (Accessors)
[CODE]
public function getAvatarSrcAttribute(): ?string
{return ImageStorage::url($this->avatar)}3. Atribut Virtual (Accessors)

[CODE]
public function getInitialsAttribute(): string
{return Str::of($this->name)->explode(' ')->filter()->take(2)
->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');}

Metode yang diawali kata get dan diakhiri Attribute disebut Accessor. Ini membuat properti bayangan yang tidak ada di database, tetapi bisa dipanggil layaknya kolom 
biasa di tampilan Blade:
getAvatarSrcAttribute() -> dipanggil dengan $user->avatar_src
• Mengubah nama file foto yang tersimpan di kolom avatar menjadi alamat URL gambar yang lengkap melalui kelas bantuan ImageStorage::url().
• Tanda ?string berarti hasilnya bisa berupa teks URL atau null jika pengguna belum mengunggah foto profil.
getInitialsAttribute() -> dipanggil dengan $user->initials
• Membuat inisial 2 huruf kapital dari nama pengguna (berguna jika pengguna tidak memakai foto profil).
• Alur kerjanya: Nama dipotong berdasarkan spasi (explode(' ')), dibersihkan dari spasi kosong (filter()), diambil maksimal 2 kata pertama (take(2)), 
lalu diambil huruf pertama masing-masing kata dan diubah menjadi huruf besar (mb_strtoupper). Contohnya, "Achmad Jauhari" otomatis menghasilkan "AJ"
```