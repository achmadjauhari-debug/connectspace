<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LocationSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_search_for_city_names_through_openstreetmap(): void
    {
        Cache::flush();
        Http::fake([
            'https://nominatim.openstreetmap.org/search*' => Http::response([
                [
                    'name' => 'Bandung',
                    'lat' => '-6.9175',
                    'lon' => '107.6191',
                    'address' => [
                        'city' => 'Bandung',
                        'state' => 'Jawa Barat',
                        'country' => 'Indonesia',
                    ],
                ],
            ]),
        ]);
        $user = User::create([
            'name' => 'Location Search Test',
            'email' => 'location-search-test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user)
            ->postJson(route('locations.search'), ['query' => 'Bandung'])
            ->assertOk()
            ->assertExactJson([
                'locations' => [
                    ['name' => 'Bandung', 'context' => 'Jawa Barat, Indonesia'],
                ],
            ]);

        $this->postJson(route('locations.search'), ['query' => 'Bandung'])
            ->assertOk()
            ->assertExactJson([
                'locations' => [
                    ['name' => 'Bandung', 'context' => 'Jawa Barat, Indonesia'],
                ],
            ]);

        Http::assertSentCount(1);
        Http::assertSent(fn (ClientRequest $request) => $request['featureType'] === 'city'
            && $request['q'] === 'Bandung'
            && $request->hasHeader('User-Agent', 'ConnectSpace/1.0'));
    }

    public function test_search_throttles_uncached_queries_globally(): void
    {
        Cache::shouldReceive('has')->once()->andReturn(false);
        Cache::shouldReceive('add')->once()->andReturn(false);
        $user = User::create([
            'name' => 'Location Search Test',
            'email' => 'location-search-throttle@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user)
            ->postJson(route('locations.search'), ['query' => 'Jakarta'])
            ->assertTooManyRequests();
    }
}
