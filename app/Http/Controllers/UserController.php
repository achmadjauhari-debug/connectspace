<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    /** Profil publik pengguna lain (atau diri sendiri). */
    public function show(User $user)
    {
        $posts = $user->posts()
            ->withCount(['likes', 'comments'])
            ->orderByDesc('created_at')->orderByDesc('id')
            ->paginate(10);

        return view('users.show', ['profile' => $user, 'posts' => $posts]);
    }
}
