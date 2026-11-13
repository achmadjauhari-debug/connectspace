<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PostLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_save_the_selected_city_with_a_post(): void
    {
        $user = User::create([
            'name' => 'Post Location Test',
            'email' => 'post-location-test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user)
            ->post(route('posts.store'), [
                'content' => 'A post with a selected city',
                'location' => 'Jakarta Selatan',
            ])
            ->assertRedirect(route('feed'));

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'content' => 'A post with a selected city',
            'location' => 'Jakarta Selatan',
        ]);
    }

    public function test_selected_city_is_shown_in_post_metadata_instead_of_profile_location(): void
    {
        $user = User::create([
            'name' => 'Post Location Test',
            'email' => 'post-location-display@example.com',
            'password' => Hash::make('password'),
            'location' => 'Profile coordinates',
        ]);
        Post::create([
            'user_id' => $user->id,
            'content' => 'A post with a selected city',
            'location' => 'Jakarta Selatan',
        ]);

        $this->actingAs($user)
            ->get(route('feed'))
            ->assertOk()
            ->assertSee('Jakarta Selatan')
            ->assertSee('OpenStreetMap contributors')
            ->assertDontSee('Profile coordinates');
    }
}
