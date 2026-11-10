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
    public function store(Request $request, Post $post)
    {
        $data = $request->validateWithBag('comment_' . $post->id, [
            'comment' => ['nullable', 'string', 'max:1000', 'required_without_all:image,image_url'],
            'image' => Pesan::fileRule(),
            'image_url' => Pesan::urlRule(),
        ], Pesan::messages(), Pesan::attributes());

        Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'comment' => $data['comment'] ?? null,
            'image' => ImageStorage::store($request, 'image', 'image_url', 'comments'),
        ]);

        return redirect(url()->previous() . '#post-' . $post->id)->with('success', 'Komentar terkirim.');
    }
}
