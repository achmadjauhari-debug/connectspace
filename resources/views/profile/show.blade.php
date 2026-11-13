@extends('layouts.app')
@section('title', $profile->name . ' · ConnectSpace')

@section('content')
    <section class="cs-card p-4 mb-4">
        <div class="d-flex align-items-center gap-3">
            <x-avatar :user="$profile" :size="88" />
            <div class="flex-grow-1">
                <div class="d-flex align-items-baseline flex-wrap gap-2">
                    <h1 class="h4 fw-bold mb-0">{{ $profile->name }}</h1>
                    <span class="meta"
                        data-user-location="{{ $profile->id }}">{{ $profile->location ?: 'Lokasi belum tersedia' }}</span>
                </div>
                <div class="meta">Bergabung sejak {{ $profile->created_at->format('d/M/Y') }} · {{ $posts->total() }}
                    status</div>
            </div>
            @if ($profile->id === auth()->id())
                <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-secondary">Edit profil</a>
            @endif
        </div>
        @if ($profile->bio)
            <p class="post-text mt-3 mb-0">{{ $profile->bio }}</p>
        @endif
    </section>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h6 fw-bold mb-0">Status oleh {{ $profile->name }}</h2>
        <a href="{{ route('feed') }}" class="small">← Kembali ke Beranda</a>
    </div>

    @forelse ($posts as $post)
        <article class="cs-card p-3 mb-3">
            <div class="mb-2">
                <x-user-meta :user="$profile" :time="$post->created_at" :location="$post->location" :show-profile-location="false" />
            </div>
            @if ($post->content)
                <p class="post-text mb-2">{{ $post->content }}</p>
            @endif
            @if ($post->image)
                <img src="{{ $post->image_src }}" alt="Gambar status dari {{ $profile->name }}" class="post-img mb-2"
                    loading="lazy">
            @endif
            <div class="meta">{{ $post->likes_count }} suka · {{ $post->comments_count }} komentar</div>
        </article>
    @empty
        <div class="cs-card p-4 text-center">
            <p class="mb-0">{{ $profile->name }} belum membuat status.</p>
        </div>
    @endforelse

    <div class="mt-3">{{ $posts->links('pagination::bootstrap-5') }}</div>
@endsection
