<?php

namespace App\Http\Controllers;

use App\Support\ImageStorage;
use App\Support\Pesan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => Pesan::fileRule(),
            'avatar_url' => Pesan::urlRule(),
        ], Pesan::messages(), Pesan::attributes());

        $user = Auth::user();
        $newAvatar = ImageStorage::store($request, 'avatar', 'avatar_url', 'avatars');

        if ($newAvatar) {
            ImageStorage::delete($user->avatar);
            $user->avatar = $newAvatar;
        } elseif ($request->boolean('remove_avatar')) {
            ImageStorage::delete($user->avatar);
            $user->avatar = null;
        }

        $user->name = $data['name'];
        $user->bio = $data['bio'] ?? null;
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validateWithBag('password', [
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:6', 'confirmed', 'different:current_password'],
        ], Pesan::messages(), Pesan::attributes());

        $user = Auth::user();

        // Wajib verifikasi password lama sebelum menyimpan sandi baru.
        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.'], 'password');
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Password berhasil diubah.');
    }
}
