<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Guest guard: pengguna yang sudah login tidak boleh membuka Login/Register. */
class RedirectIfLoggedIn
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return redirect()->route('feed');
        }

        return $next($request);
    }
}
