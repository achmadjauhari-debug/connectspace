PROSES LOGOUT

```php
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah keluar.');
    }
}
Fungsi logout() tersebut adalah metode standar di Laravel untuk mengeluarkan pengguna dari sesi login secara aman, 
membersihkan seluruh jejak sesi browser yang lama, lalu mengembalikan pengguna ke halaman login dengan membawa pesan notifikasi.

Rincian Alur Kerja Kode

1. Menghapus status login (Auth::logout())
	• Mengakhiri status terautentikasi pengguna di dalam sistem Laravel.
	• Jika sebelumnya pengguna mencentang fitur "Ingat Saya" (Remember Me), perintah ini juga otomatis menghapus cookie jangka panjang di browser serta mengacak ulang kolom remember_token di database.
2. Menghanguskan sesi saat ini ($request->session()->invalidate())
	• Menghapus seluruh data sesi yang tersimpan di server untuk pengguna tersebut dan membuat ID sesi baru.
	• Langkah ini sangat penting untuk mencegah celah keamanan, misalnya jika ada pihak lain yang mencoba memakai ulang ID sesi lama atau menekan tombol Back di browser setelah pengguna keluar.
3. Memperbarui token keamanan CSRF ($request->session()->regenerateToken())
	• Membuat ulang token @csrf (Cross-Site Request Forgery) yang baru.
	• Ini mencegah form lama yang mungkin masih terbuka di tab lain mengirimkan permintaan palsu menggunakan token sebelum logout.
4. Mengalihkan halaman dan mengirim pesan (return redirect()->route('login')->with(...))
	• Mengarahkan browser pengguna kembali ke halaman dengan nama rute login.
	• Menyisipkan pesan sementara (flash session) 'success' bertuliskan "Anda telah keluar." yang nantinya akan ditangkap oleh kode Blade @if (session('success')) milik Anda.
```