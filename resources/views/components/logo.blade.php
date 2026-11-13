@props(['size' => 36, 'dark' => false, 'tagline' => false])
@php
    $ink   = $dark ? '#ffffff' : '#12262a';
    $space = $dark ? '#7fd6dc' : '#0b6e75';
    $tag   = $dark ? '#a9c2c5' : '#5d7377';
@endphp
<span class="d-inline-flex align-items-center" style="gap:{{ round($size * 0.28) }}px" role="img" aria-label="ConnectSpace PPKD JP 2026">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="{{ $size }}" height="{{ $size }}" aria-hidden="true" style="flex:none">
        <defs>
            <linearGradient id="csLogoGrad" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="#12a0a9"/>
                <stop offset="1" stop-color="#084f55"/>
            </linearGradient>
        </defs>
        <rect width="64" height="64" rx="16" fill="url(#csLogoGrad)"/>
        <path d="M42.72 23 A14 14 0 1 0 42.72 41" fill="none" stroke="#fff" stroke-width="6" stroke-linecap="round"/>
        <path d="M42.72 27.5 V36.5" stroke="#fff" stroke-opacity=".6" stroke-width="2.4" stroke-linecap="round"/>
        <circle cx="42.72" cy="23" r="4.6" fill="#fff"/>
        <circle cx="42.72" cy="41" r="4.6" fill="#ffb454"/>
    </svg>
    <span style="line-height:1.05;text-align:left">
        <span style="display:block;font-weight:800;letter-spacing:-.02em;font-size:{{ round($size * 0.62) }}px;color:{{ $ink }}">Connect<span style="font-weight:500;color:{{ $space }}">Space</span></span>
        @if ($tagline)
            <span style="display:block;font-weight:700;letter-spacing:.22em;font-size:{{ max(9, round($size * 0.24)) }}px;color:{{ $tag }};margin-top:3px">PPKD JP 2026</span>
        @endif
    </span>
</span>
