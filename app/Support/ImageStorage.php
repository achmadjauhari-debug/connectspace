<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Penanganan gambar: upload berkas (disk "public") atau URL gambar.
 */
class ImageStorage
{
    public static function store(Request $request, string $fileField, string $urlField, string $dir): ?string
    {
        if ($request->hasFile($fileField)) {
            // hashName() + ekstensi dari MIME hasil deteksi -> nama file aman
            return $request->file($fileField)->store($dir, 'public');
        }

        $url = $request->input($urlField);

        return $url ? (string) $url : null;
    }

    public static function isUrl(?string $value): bool
    {
        return (bool) ($value && preg_match('#^https?://#i', $value));
    }

    public static function delete(?string $value): void
    {
        if ($value && ! self::isUrl($value)) {
            Storage::disk('public')->delete($value);
        }
    }

    public static function url(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return self::isUrl($value) ? $value : asset('storage/' . $value);
    }
}
