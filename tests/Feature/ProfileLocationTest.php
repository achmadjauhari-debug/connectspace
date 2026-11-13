<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_save_browser_coordinates(): void
    {
        $user = User::create([
            'name' => 'Location Test',
            'email' => 'location-test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)->postJson(route('profile.location.update'), [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
        ]);

        $response->assertOk()->assertExactJson(['location' => '-6.2088, 106.8456']);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'location' => '-6.2088, 106.8456',
        ]);
    }

    public function test_location_update_rejects_coordinates_outside_geographic_ranges(): void
    {
        $user = User::create([
            'name' => 'Location Test',
            'email' => 'location-test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user)
            ->postJson(route('profile.location.update'), [
                'latitude' => 91,
                'longitude' => 181,
            ])
            ->assertUnprocessable();
    }
}
