<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LocationSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:100'],
        ]);
        $query = Str::squish($validated['query']);
        $cacheKey = 'openstreetmap.nominatim.search.'.sha1(Str::lower($query));

        if (Cache::has($cacheKey)) {
            return response()->json(['locations' => Cache::get($cacheKey)]);
        }

        if (! Cache::add('openstreetmap.nominatim.throttle', true, now()->addSecond())) {
            return response()->json([
                'message' => 'Tunggu sebentar sebelum mencari kota lagi.',
            ], 429)->header('Retry-After', '1');
        }

        try {
            $http = Http::acceptJson()
                ->withHeaders([
                    'User-Agent' => config('services.openstreetmap.user_agent'),
                    'Referer' => config('app.url'),
                ])
                ->timeout(5);
            $caBundle = config('services.openstreetmap.ca_bundle');

            if ($caBundle) {
                $http->withOptions(['verify' => $caBundle]);
            }

            $response = $http->get(rtrim(config('services.openstreetmap.nominatim_url'), '/').'/search', [
                'q' => $query,
                'format' => 'jsonv2',
                'addressdetails' => 1,
                'featureType' => 'city',
                'limit' => 5,
                'accept-language' => 'id',
            ]);
        } catch (ConnectionException $exception) {
            Log::warning('OpenStreetMap city search connection failed.', [
                'exception' => $exception::class,
            ]);

            return response()->json([
                'message' => 'Layanan pencarian kota sedang tidak tersedia. Coba lagi nanti.',
            ], 502);
        }

        if ($response->failed()) {
            Log::warning('OpenStreetMap city search returned an error.', [
                'status' => $response->status(),
            ]);

            return response()->json([
                'message' => 'Layanan pencarian kota sedang tidak tersedia. Coba lagi nanti.',
            ], 502);
        }

        $locations = collect($response->json())
            ->map(function ($place) {
                if (! is_array($place)) {
                    return null;
                }

                $address = $place['address'] ?? [];
                $name = $address['city']
                    ?? $address['town']
                    ?? $address['municipality']
                    ?? $address['village']
                    ?? $place['name']
                    ?? null;

                if (! is_string($name) || trim($name) === '') {
                    return null;
                }

                $context = array_filter([
                    $address['state'] ?? $address['province'] ?? null,
                    $address['country'] ?? null,
                ], fn ($value) => is_string($value) && trim($value) !== '');

                return [
                    'name' => trim($name),
                    'context' => implode(', ', $context),
                ];
            })
            ->filter()
            ->unique(fn (array $location) => Str::lower($location['name'].'|'.$location['context']))
            ->take(5)
            ->values()
            ->all();

        Cache::put($cacheKey, $locations, now()->addDays(30));

        return response()->json(['locations' => $locations]);
    }
}
