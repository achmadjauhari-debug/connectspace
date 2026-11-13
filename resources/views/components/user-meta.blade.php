@props(['user', 'time', 'location' => null, 'showProfileLocation' => true])
@php($displayLocation = $showProfileLocation ? $user->location : $location)
<span {{ $attributes->merge(['class' => 'meta']) }}>
    @if ($displayLocation)
        <span>{{ $displayLocation }}</span>
        @unless ($showProfileLocation)
            <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">
                © OpenStreetMap contributors
            </a>
        @endunless
        ·
    @endif
    <time datetime="{{ $time->toIso8601String() }}">{{ $time->format('d/m/Y H:i') }}</time>
</span>
