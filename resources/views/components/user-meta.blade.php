@props(['user', 'time', 'location' => null, 'showProfileLocation' => true])
@php($displayLocation = $showProfileLocation ? $user->location : $location)
<span {{ $attributes->merge(['class' => 'meta']) }}>
    @if ($displayLocation)
        <span>{{ $displayLocation }}</span>

        ·
    @endif
    <time datetime="{{ $time->toIso8601String() }}"
        title="{{ $time->format('d/M/y H:i') }}">{{ $time->locale('id')->diffForHumans() }}</time>
</span>
