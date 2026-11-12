<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Pesan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

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
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    //**************//
    // PROSES LOGIN //
    //**************//

    public function login(Request $request)
    {
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        // fungsi validate untuk memastikan kelengkapan dan format data. i.e email harus ada @, password harus di isi
        // jika terpenuhi fungsi ini akan mengambil data array email dan password dan menyimpannya di variable $credentials 

        $credentials = $request->validate([
            'email'     => ['required', 'email'],
            'password'  => ['required'],
        ], Pesan::messages(), Pesan::attributes());

        // Pesan::message Fungsi utamanya adalah mengubah pesan kesalahan validasi bawaan Laravel (yang berbahasa Inggris) menjadi pesan berbahasa Indonesia yang lebih rapi dan mudah dipahami pengguna, tanpa harus menuliskannya berulang-ulang di setiap Controller.

        // Metode $request->validate() di Laravel sebenarnya menerima tiga parameter: (1) Aturan validasi, (2) Pesan error kustom, dan (3) Nama atribut/kolom kustom. Kelas Pesan menyediakan parameter kedua dan ketiga tersebut

        // Auth::attempt memverifikasi password terhadap hash di database.
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
        // Verifikasi dan fitur "Ingat Saya". Fungsi Auth::attempt($credentials, $request->boolean('remember')) mencocokkan email dan kata sandi dengan database. Parameter kedua ($request->boolean('remember')) membaca apakah kotak centang (checkbox) bernama remember di form login dicentang atau tidak (true atau false). Jika dicentang, Laravel membuat cookie jangka panjang agar pengguna tetap login meski browser ditutup.

        // Pembaruan sesi keamanan. Perintah $request->session()->regenerate() memperbarui ID sesi di server untuk mencegah serangan pembajakan sesi (session fixation).

            return redirect()->route('feed');

        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');

        // Metode ->onlyInput('email') menyimpan kembali teks email yang baru saja diketik pengguna agar tidak hilang saat halaman dimuat ulang. Password sengaja tidak ikut dikembalikan demi alasan keamanan
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Perintah $request->session()->regenerate() dan invalidate().
        // Baris-baris ini hanya bertugas mengatur keamanan ID sesi login (membuat ulang token sesi) dan tidak mengirimkan teks pesan notifikasi apa pun ke halaman Blade.

        return redirect()->route('login')->with('success', 'Anda telah keluar.');
    }
}
