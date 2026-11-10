<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Auth guard: tamu dilempar ke Login dengan notifikasi peringatan. */
class EnsureAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('warning', 'Silakan login terlebih dahulu untuk mengakses halaman tersebut.');
        }

        return $next($request);
    }
}
