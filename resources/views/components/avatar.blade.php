@props(['user', 'size' => 40])
@if ($user->avatar)
    <img src="{{ $user->avatar_src }}" alt="Foto profil {{ $user->name }}" class="avatar"
         style="width:{{ $size }}px;height:{{ $size }}px" loading="lazy">
@else
    <span class="avatar avatar-fallback" aria-hidden="true"
          style="width:{{ $size }}px;height:{{ $size }}px;font-size:{{ round($size * 0.4) }}px">{{ $user->initials }}</span>
@endif
