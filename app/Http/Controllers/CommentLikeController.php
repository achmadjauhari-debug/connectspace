<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentLike;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class CommentLikeController extends Controller
{
    /** Toggle like/unlike pada komentar atau balasan. */
    public function toggle(Comment $comment)
    {
        $attrs = ['user_id' => Auth::id(), 'comment_id' => $comment->id];

        if (! CommentLike::where($attrs)->delete()) {
            try {
                CommentLike::create($attrs);
            } catch (QueryException $e) {
                // 23000 = pelanggaran UNIQUE (klik ganda): abaikan, tetap 1 like.
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
            }
        }

        return redirect(url()->previous() . '#comment-' . $comment->id);
    }
}
