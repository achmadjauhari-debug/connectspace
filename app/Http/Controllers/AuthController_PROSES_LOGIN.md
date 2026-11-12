```
PROSES LOGIN
============
use Illuminate\Support\Facades\Hash;
• Fungsi utama: Mengimpor Facade Hash untuk mengenkripsi dan memverifikasi kata sandi secara aman (menggunakan algoritma seperti Bcrypt atau Argon2).
• Contoh penggunaan di kode Anda: Memanggil Hash::make($data['password']) saat registrasi agar password tidak disimpan sebagai teks biasa di database, 
melainkan sebagai kode acak yang tidak bisa dibaca manusia.

use Illuminate\Support\Facades\Auth;
• Fungsi utama: Mengimpor Facade Auth untuk mengatur seluruh proses autentikasi dan sesi login pengguna.
• Contoh penggunaan di kode Anda: Memanggil Auth::login($user) untuk meloginkan pengguna baru, 
Auth::attempt($credentials) untuk mencocokkan email dan password saat login, serta Auth::logout() untuk mengeluarkan pengguna dari sesi.

use Illuminate\Support\Str;
• Fungsi utama: Mengimpor kelas bantuan (helper) Str yang menyediakan berbagai fungsi praktis untuk memanipulasi teks (string).
• Contoh penggunaan di kode Anda: Memanggil Str::lower(...) untuk mengubah semua huruf pada alamat email menjadi huruf kecil sebelum diproses lebih lanjut.

use Illuminate\Http\Request;
• Asal kelas: Mengimpor kelas inti bawaan dari framework Laravel (berada di dalam folder vendor/laravel/framework/...).
• Kegunaan: Mewakili seluruh data permintaan HTTP dari pengguna (seperti input form email, password, cookie, dan sesi). 
  Kelas inilah yang digunakan sebagai tipe data parameter (Request $request) pada fungsi register(), login(), dan logout().

[KODE]
$request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

Baris kode tersebut berfungsi untuk membersihkan dan menyeragamkan format alamat email yang dikirimkan pengguna 
sebelum diperiksa oleh validasi atau dicocokkan dengan database.
Kode ini bekerja dari bagian paling dalam ke bagian paling luar, lalu menimpa data email yang lama di dalam objek $request.

Urutan Proses dari Dalam ke Luar

• 1. $request->input('email'). Mengambil nilai teks mentah yang diketik pengguna pada kolom input email.
• 2. (string) .... Memastikan nilai tersebut bertipe teks (string). Langkah ini mencegah error jika pengguna tidak mengisi kolom tersebut (null) atau mengirim tipe data aneh.
• 3. trim(...). Menghapus spasi kosong berlebih di awal dan akhir teks (misalnya jika pengguna tidak sengaja menyalin email beserta spasinya: " user@gmail.com " menjadi "user@gmail.com").
• 4. Str::lower(...). Mengubah seluruh huruf kapital menjadi huruf kecil menggunakan helper bawaan Laravel (misalnya "User@Gmail.COM" menjadi "user@gmail.com").
• 5. $request->merge(['email' => ...]). Menimpa nilai email di dalam $request dengan hasil yang sudah bersih. Jadi, baris kode di bawahnya ($request->validate(...)) 
     akan langsung menerima email yang rapi.

[CODE]
$credentials = $request->validate([
      'email'     => ['required', 'email'],
      'password'  => ['required'],
 ], Pesan::messages(), Pesan::attributes());

Fungsi validate untuk memastikan kelengkapan dan format data. i.e email harus ada @, password harus di isi
jika terpenuhi fungsi ini akan mengambil data array email dan password dan menyimpannya di variable $credentials

Pesan::message Fungsi utamanya adalah mengubah pesan kesalahan validasi bawaan Laravel (yang berbahasa Inggris) menjadi pesan berbahasa Indonesia yang lebih rapi 
dan mudah dipahami pengguna, tanpa harus menuliskannya berulang-ulang di setiap Controller.
Metode $request->validate() di Laravel sebenarnya menerima tiga parameter: 
(1) Aturan validasi, (2) Pesan error kustom, dan (3) Nama atribut/kolom kustom. Kelas Pesan menyediakan parameter kedua dan ketiga tersebut
   
[CODE]
 if (Auth::attempt($credentials, $request->boolean('remember')
        $request->session()->regenerate();
        return redirect()->route('feed');
 

    return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');

Proses tersebut bekerja melalui kerja sama antara HTML Form, metode Auth::attempt(), kolom remember_token di database, dan cookie khusus di browser pengguna.
Saat kotak centang dicentang, Laravel membuat token acak yang disimpan di database sekaligus di browser sebagai cookie dengan masa berlaku hingga beberapa tahun.

1. Tahap Pengiriman Sinyal dari Form HTML

Di halaman login (file Blade), terdapat elemen kotak centang dengan atribut name="remember":
<input type="checkbox" name="remember" id="remember">
<label for="remember">Ingat Saya</label>
Gunakan kode dengan hati-hati.
• Jika dicentang: Browser mengirimkan data remember = "on" (atau "1") saat tombol login ditekan.
• Jika tidak dicentang: Browser tidak mengirimkan data remember sama sekali.


2. Tahap Penerjemahan di Controller

Di dalam AuthController, baris $request->boolean('remember') menerjemahkan input dari form tadi menjadi nilai logika murni:
• Mengembalikan true jika kotak dicentang.
• Mengembalikan false jika kotak tidak dicentang.
Nilai true atau false ini kemudian dimasukkan sebagai parameter kedua ke dalam Auth::attempt($credentials, true).


3. Tahap Pembuatan Token & Cookie oleh Laravel

Ketika Auth::attempt() menerima nilai true dan password terbukti cocok, Laravel otomatis menjalankan tiga langkah di balik layar:
• Membuat token acak. Laravel menghasilkan string acak sepanjang 60 karakter yang sangat sulit ditebak.
• Menyimpan ke database. Token tersebut disimpan ke dalam kolom remember_token pada tabel users milik pengguna yang sedang login. 
  (Itulah sebabnya tabel users bawaan Laravel selalu memiliki kolom remember_token).
• Menanamkan Cookie jangka panjang di browser. Laravel mengirimkan cookie khusus bernama remember_web_... ke browser pengguna. 
  Cookie ini berisi gabungan ID pengguna, remember_token, dan password hash yang sudah dienkripsi, 
  dengan masa kedaluwarsa yang sangat lama (secara bawaan diatur hingga 400 hari atau lebih, tergantung versi Laravel).


4. Bagaimana Pengguna Tetap Login Saat Browser Ditutup?

Sesi login biasa (laravel_session) akan hilang saat browser ditutup atau setelah 2 jam tidak aktif. Namun, ketika pengguna membuka kembali website Anda keesokan harinya:
1. Middleware autentikasi Laravel melihat bahwa sesi biasa sudah habis.
2. Laravel otomatis memeriksa apakah browser pengguna membawa cookie remember_web_....
3. Jika ada, Laravel mencocokkan isi cookie tersebut dengan remember_token yang tersimpan di tabel users database.
4. Jika cocok, Laravel langsung meloginkan pengguna secara diam-diam tanpa perlu menampilkan halaman login lagi.

Catatan Keamanan Penting
Ketika pengguna menekan tombol Logout (Auth::logout()), Laravel secara otomatis akan mengacak ulang nilai remember_token di database 
dan menghapus cookie remember_web_... di browser. 
Ini memastikan cookie lama tidak bisa dipakai lagi setelah pengguna keluar.
```