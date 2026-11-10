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
        $posts = Post::with(['user', 'comments.user'])
            ->withCount('likes')
            ->withExists(['likes as liked_by_me' => fn ($q) => $q->where('user_id', Auth::id())])
            ->orderByDesc('created_at')->orderByDesc('id') // terbaru di atas
            ->paginate(10);

        return view('feed.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => ['nullable', 'string', 'max:2000', 'required_without_all:image,image_url'],
            'image' => Pesan::fileRule(),
            'image_url' => Pesan::urlRule(),
        ], Pesan::messages(), Pesan::attributes());

        Post::create([
            'user_id' => Auth::id(),
            'content' => $data['content'] ?? null,
            'image' => ImageStorage::store($request, 'image', 'image_url', 'posts'),
        ]);

        return redirect()->route('feed')->with('success', 'Status berhasil dikirim.');
    }

    public function destroy(Post $post)
    {
        // Otorisasi: hanya pemilik postingan yang boleh menghapus.
        abort_unless($post->user_id === Auth::id(), 403, 'Anda tidak berhak menghapus postingan ini.');

        $files = $post->comments()->pluck('image')->push($post->image);

        $post->delete(); // likes & comments ikut terhapus (ON DELETE CASCADE)

        $files->each(fn ($f) => ImageStorage::delete($f));

        return back()->with('success', 'Status berhasil dihapus.');
    }
}
