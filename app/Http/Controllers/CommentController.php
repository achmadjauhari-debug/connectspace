<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Support\ImageStorage;
use App\Support\Pesan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    private function rules(): array
    {
        return [
            'comment' => ['nullable', 'string', 'max:500', 'required_without_all:image,image_url'],
            'image' => Pesan::fileRule(),
            'image_url' => Pesan::urlRule(),
        ];
    }

    /** Komentar utama pada sebuah status. */
    public function store(Request $request, Post $post)
    {
        $data = $request->validateWithBag('comment_' . $post->id, $this->rules(), Pesan::messages(), Pesan::attributes());

        Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'comment' => $data['comment'] ?? null,
            'image' => ImageStorage::store($request, 'image', 'image_url', 'comments'),
        ]);

        return redirect(url()->previous() . '#post-' . $post->id)->with('success', 'Komentar terkirim.');
    }

    /** Balasan atas komentar orang lain (atau milik sendiri). */
    public function reply(Request $request, Comment $comment)
    {
        $data = $request->validateWithBag('reply_' . $comment->id, $this->rules(), Pesan::messages(), Pesan::attributes());

        // Maksimal 2 tingkat: membalas sebuah balasan tetap dipasang ke komentar utamanya.
        $parent = $comment->parent_id ? $comment->parent : $comment;

        Comment::create([
            'post_id' => $parent->post_id,
            'parent_id' => $parent->id,
            'user_id' => Auth::id(),
            'comment' => $data['comment'] ?? null,
            'image' => ImageStorage::store($request, 'image', 'image_url', 'comments'),
        ]);

        return redirect(url()->previous() . '#comment-' . $parent->id)->with('success', 'Balasan terkirim.');
    }

    /** Hapus komentar/balasan milik sendiri, termasuk gambarnya. */
    public function destroy(Comment $comment)
    {
        // Otorisasi: hanya pemilik komentar.
        abort_unless($comment->user_id === Auth::id(), 403, 'Anda tidak berhak menghapus komentar ini.');

        $postId = $comment->post_id;

        // Kumpulkan berkas gambar komentar ini + semua balasannya sebelum baris dihapus.
        $files = $comment->replies()->pluck('image')->push($comment->image);

        $comment->delete(); // balasan & like komentar ikut terhapus (ON DELETE CASCADE)

        $files->each(fn ($f) => ImageStorage::delete($f));

        return redirect(url()->previous() . '#post-' . $postId)->with('success', 'Komentar berhasil dihapus.');
    }
}
