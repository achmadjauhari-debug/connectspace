<?php

namespace App\Support;

/**
 * Pesan validasi berbahasa Indonesia.
 */
class Pesan
{
    public static function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'email' => ':attribute harus berformat email yang valid.',
            'unique' => ':attribute sudah terdaftar.',
            'min.string' => ':attribute minimal :min karakter.',
            'max.string' => ':attribute maksimal :max karakter.',
            'max.file' => 'Ukuran :attribute maksimal :max KB.',
            'confirmed' => 'Konfirmasi :attribute tidak cocok.',
            'different' => ':attribute baru harus berbeda dari password lama.',
            'image' => ':attribute harus berupa gambar.',
            'mimes' => ':attribute harus berformat jpg, jpeg, png, gif, atau webp.',
            'uploaded' => ':attribute gagal diunggah (periksa ukuran berkas).',
            'url' => ':attribute harus berupa URL yang valid.',
            'regex' => ':attribute harus diawali http:// atau https://.',
            'content.required_without_all' => 'Isi teks status atau lampirkan gambar (salah satu wajib ada).',
            'comment.required_without_all' => 'Isi teks komentar atau lampirkan gambar (salah satu wajib ada).',
        ];
    }

    public static function attributes(): array
    {
        return [
            'name' => 'Nama',
            'email' => 'Email',
            'password' => 'Password',
            'password_confirmation' => 'Konfirmasi password',
            'current_password' => 'Password lama',
            'bio' => 'Bio',
            'location' => 'Lokasi',
            'avatar' => 'Foto profil',
            'avatar_url' => 'URL foto profil',
            'content' => 'Status',
            'comment' => 'Komentar',
            'image' => 'Gambar',
            'image_url' => 'URL gambar',
        ];
    }

    /** Aturan umum untuk input gambar: berkas + URL. */
    public static function fileRule(): array
    {
        return ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'];
    }

    public static function urlRule(): array
    {
        return ['nullable', 'url', 'max:255', 'regex:#^https?://#i'];
    }
}
