<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CommentReplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_reply_can_be_replied_to_in_the_original_comment_thread(): void
    {
        $user = User::create([
            'name' => 'Reply Test',
            'email' => 'reply-test@example.com',
            'password' => Hash::make('password'),
        ]);
        $post = Post::create([
            'user_id' => $user->id,
            'content' => 'Post for reply testing',
        ]);
        $root = Comment::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'comment' => 'Original comment',
        ]);
        $reply = Comment::create([
            'post_id' => $post->id,
            'parent_id' => $root->id,
            'user_id' => $user->id,
            'comment' => 'First reply',
        ]);

        $this->actingAs($user)
            ->get(route('feed'))
            ->assertOk()
            ->assertSee(route('comments.reply', $reply), false);

        $this->actingAs($user)
            ->post(route('comments.reply', $reply), ['comment' => 'Reply to the reply'])
            ->assertSessionHas('success', 'Balasan terkirim.');

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'parent_id' => $root->id,
            'comment' => 'Reply to the reply',
        ]);
    }
}
