<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle(Post $post)
    {
        $attrs = ['user_id' => Auth::id(), 'post_id' => $post->id];

        // Sudah like -> hapus (unlike). Belum -> buat like baru.
        if (! Like::where($attrs)->delete()) {
            try {
                Like::create($attrs);
            } catch (QueryException $e) {
                // 23000 = pelanggaran UNIQUE (klik ganda/bersamaan): abaikan, tetap 1 like.
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
            }
        }

        return redirect(url()->previous() . '#post-' . $post->id);
    }
}
