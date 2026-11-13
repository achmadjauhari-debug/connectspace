<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Support\ImageStorage;
use App\Support\Pesan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    public function index()
    {
        // Hitung like + status "sudah saya like" untuk komentar dan balasan sekaligus (tanpa N+1).
        $withLikes = fn ($q) => $q->withCount('likes')
            ->withExists(['likes as liked_by_me' => fn ($l) => $l->where('user_id', Auth::id())]);

        $posts = Post::with([
            'user',
            'rootComments' => $withLikes,
            'rootComments.user',
            'rootComments.replies' => $withLikes,
            'rootComments.replies.user',
        ])
            ->withCount(['likes', 'comments']) // comments_count sudah termasuk balasan
            ->withExists(['likes as liked_by_me' => fn ($q) => $q->where('user_id', Auth::id())])
            ->orderByDesc('created_at')->orderByDesc('id') // terbaru di atas
            ->paginate(10);

        return view('feed.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => ['nullable', 'string', 'max:500', 'required_without_all:image,image_url'],
            'image' => Pesan::fileRule(),
            'image_url' => Pesan::urlRule(),
            'location' => ['nullable', 'string', 'max:100'],
        ], Pesan::messages(), Pesan::attributes());

        Post::create([
            'user_id' => Auth::id(),
            'content' => $data['content'] ?? null,
            'image' => ImageStorage::store($request, 'image', 'image_url', 'posts'),
            'location' => $data['location'] ?? null,
        ]);

        return redirect()->route('feed')->with('success', 'Status berhasil dikirim.');
    }

    public function destroy(Post $post)
    {
        // Otorisasi: hanya pemilik postingan yang boleh menghapus.
        abort_unless($post->user_id === Auth::id(), 403, 'Anda tidak berhak menghapus postingan ini.');

        // comments() mencakup komentar utama + balasan, jadi semua gambar ikut dibersihkan.
        $files = $post->comments()->pluck('image')->push($post->image);

        $post->delete(); // likes, komentar, balasan & like komentar ikut terhapus (CASCADE)

        $files->each(fn ($f) => ImageStorage::delete($f));

        return back()->with('success', 'Status berhasil dihapus.');
    }
}
