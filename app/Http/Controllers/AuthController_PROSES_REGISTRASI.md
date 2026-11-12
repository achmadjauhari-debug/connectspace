PROSES REGISTRASI

```php
    public function register(Request $request)
    {
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], Pesan::messages(), Pesan::attributes());

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        // Auto-login: langsung buat sesi aktif, TANPA lewat form login.
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('feed')
            ->with('success', 'Selamat datang, ' . $user->name . '! Akun Anda berhasil dibuat.');

Fungsi register() tersebut adalah metode di dalam AuthController yang menangani seluruh proses pendaftaran akun baru di Laravel, 
mulai dari membersihkan input pengguna, memvalidasi data, menyimpan akun ke database, hingga meloginkan pengguna baru secara otomatis (auto-login).


1. Pembersihan Format Email
[CODE]
$request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

Menyeragamkan teks email. Kode ini menghapus spasi kosong di awal/akhir (trim) dan mengubah seluruh huruf menjadi kecil (Str::lower). 
Ini memastikan tidak ada duplikasi akun akibat perbedaan huruf besar/kecil (misalnya Budi@gmail.com dianggap sama dengan budi@gmail.com).


2. Validasi Data Pendaftaran
[CODE]
$data = $request->validate([
    'name' => ['required', 'string', 'max:100'],
    'email' => ['required', 'email', 'max:150', 'unique:users,email'],
    'password' => ['required', 'string', 'min:6', 'confirmed'],
], Pesan::messages(), Pesan::attributes());

• Aturan kolom name. Wajib diisi, harus berupa teks, dan maksimal 100 karakter.
• Aturan kolom email. Wajib diisi, format email sah, maksimal 150 karakter, dan unique:users,email memastikan alamat email tersebut 
  belum pernah terdaftar di tabel users.
• Aturan kolom password. Wajib diisi, minimal 6 karakter, dan confirmed mewajibkan ada input kedua bernama password_confirmation yang isinya 
  persis sama dengan kolom password.
• Pesan error bahasa Indonesia. Jika ada aturan yang gagal, kelas Pesan menampilkan pesan kesalahan yang ramah pengguna 
  dan langsung mengembalikan mereka ke form registrasi.


3. Menyimpan Pengguna Baru ke Database
[CODE]
$user = User::create([
    'name' => $data['name'],
    'email' => $data['email'],
    'password' => Hash::make($data['password']),
]);
Gunakan kode dengan hati-hati.
• Membuat baris baru di tabel users. Perintah User::create() menyimpan data yang sudah lolos validasi ke database 
  dan mengembalikan objek pengguna baru tersebut ke dalam variabel $user.
• Enkripsi kata sandi. Fungsi Hash::make() mengacak kata sandi menjadi kode rahasia sebelum disimpan agar aman dari kebocoran data.


4. Login Otomatis & Pengalihan Halaman
[CODE]
Auth::login($user);
$request->session()->regenerate();
return redirect()->route('feed')
    ->with('success', 'Selamat datang, ' . $user->name . '! Akun Anda berhasil dibuat.');

• Langsung masuk tanpa lewat form login (Auth::login($user)). Laravel langsung mencatat $user sebagai pengguna yang sedang aktif login.
• Pembaruan sesi keamanan ($request->session()->regenerate()). Membuat ID sesi baru untuk mencegah celah keamanan pembajakan sesi (session fixation).
• Arahkan ke halaman utama (redirect()->route('feed')). Mengirim pengguna ke halaman feed sambil membawa pesan sukses sementara (flash session) 
  berisi sapaan dengan nama pengguna yang baru mendaftar.
```