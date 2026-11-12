PROSES ROUTING

```php
Route::middleware(RedirectIfLoggedIn::class)->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');


Rincian Struktur Grup dan Rute

• Route::middleware(RedirectIfLoggedIn::class)->group(function () { ... })
  Pelindung grup: Menerapkan middleware RedirectIfLoggedIn (yang Anda tanyakan di awal) ke semua rute yang ada di dalam kurung kurawal sekaligus, 
  sehingga Anda tidak perlu menuliskannya satu per satu di setiap baris rute.
RUTE LOGIN
• Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
  Menampilkan halaman formulir login saat pengguna membuka URL /login di browser (GET). Rute ini diberi nama login.
• Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
  Memproses data pengiriman formulir login (POST). Dilengkapi pelindung tambahan throttle:10,1 (maksimal 10 percobaan per menit) dan diberi nama login.attempt.
RUTE REGISTRASI
• Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
  Menampilkan halaman formulir pendaftaran akun baru saat pengguna membuka URL /register (GET). Rute ini diberi nama register.
• Route::post('/register', [AuthController::class, 'register'])->name('register.store');
  Memproses penyimpanan data pendaftaran akun baru (POST) ke database melalui fungsi register() di AuthController. Rute ini diberi nama register.store.

// Hanya untuk pengguna yang sudah login (tamu dilempar ke Login)
Route::middleware(EnsureAuthenticated::class)->group(function () {
    Route::get('/', [FeedController::class, 'index'])->name('feed');
    Route::get('/posts/create', fn () => redirect(route('feed') . '#form-posting'))->name('posts.create');
    Route::post('/posts', [FeedController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{post}', [FeedController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
Kode tersebut adalah grup rute utama aplikasi (protected routes) di routes/web.php yang membungkus seluruh fitur inti 
(beranda, postingan, like, komentar, profil, dan logout) menggunakan middleware EnsureAuthenticated.
Artinya, seluruh rute di dalam blok ini hanya dapat diakses oleh pengguna yang sudah login. 
Jika pengunjung yang belum login mencoba membuka salah satu alamat di bawah ini, middleware EnsureAuthenticated akan otomatis mencegat 
dan melemparnya kembali ke halaman login.

1. Rute Beranda & Postingan (FeedController)

• Route::get('/', [FeedController::class, 'index'])->name('feed'): Menampilkan halaman utama/beranda (feed) saat pengguna membuka alamat akar website (/).
• Route::get('/posts/create', fn () => redirect(route('feed') . '#form-posting'))->name('posts.create'): Rute jalan pintas menggunakan Closure (fungsi anonim). Jika pengguna mengakses /posts/create, sistem langsung mengalihkannya ke halaman feed dan menggulir layar tepat ke elemen ber-ID #form-posting.
• Route::post('/posts', [FeedController::class, 'store'])->name('posts.store'): Menyimpan postingan baru yang dikirimkan pengguna ke database.
• Route::delete('/posts/{post}', [FeedController::class, 'destroy'])->name('posts.destroy'): Menghapus postingan tertentu. Parameter {post} menggunakan fitur Route Model Binding yang otomatis mencari data postingan berdasarkan ID di URL.

2. Rute Interaksi Sosial (LikeController & CommentController)

• Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like'): Menangani aksi menyukai atau batal menyukai (toggle like) pada suatu postingan.
• Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store'): Menyimpan komentar baru pada postingan tertentu.

3. Rute Pengaturan Profil (ProfileController)

• Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'): Menampilkan halaman form untuk mengedit profil pengguna.
• Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update'): Memperbarui data profil (seperti nama, bio, atau foto avatar) menggunakan metode HTTP PUT.
• Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password'): Memperbarui kata sandi akun pengguna secara terpisah.

4. Rute Keluar Akun (AuthController)

• Route::post('/logout', [AuthController::class, 'logout'])->name('logout'): Menjalankan proses keluar akun dengan aman menggunakan metode POST dan mengarahkan pengguna kembali ke halaman login.

Kesimpulan Arsitektur Rute Anda

Dengan menggabungkan grup RedirectIfLoggedIn (sebelumnya) dan EnsureAuthenticated (ini), sistem autentikasi aplikasi Anda sudah membentuk siklus perlindungan penuh:
• Tamu (Guest) hanya bisa membuka /login dan /register. Jika mencoba masuk ke / atau /profile, langsung dilempar ke /login.
• Pengguna Login (Authenticated) hanya bisa mengakses fitur di dalam aplikasi dan /logout. Jika mencoba membuka /login atau /register, langsung dilempar ke /.
```