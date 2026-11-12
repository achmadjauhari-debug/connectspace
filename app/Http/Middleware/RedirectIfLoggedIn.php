<?php

namespace App\Http\Middleware; //lokasi middleware

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Guest guard: pengguna yang sudah login tidak boleh membuka Login/Register. */
class RedirectIfLoggedIn //alihkan jika sudah login
{
    public function handle(Request $request, Closure $next): Response
    //Request untuk membaca permintaan dari Web
    {
        if (Auth::check()) { //periksa apakah authenticated ke dalam sistem atau belum
            return redirect()->route('feed'); //jika sudah masukan ke halaman feed
        }

        return $next($request); //kalau belum kembalikan ke halaman login
    }
}
