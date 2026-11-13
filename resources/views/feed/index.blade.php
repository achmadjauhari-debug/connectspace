@extends('layouts.app')
@section('title', 'Beranda · ConnectSpace')

@section('content')
    {{-- Form posting --}}
    <form id="form-posting" method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data"
        class="cs-card p-3 mb-4">
        @csrf
        <div class="d-flex gap-3">
            <x-avatar :user="auth()->user()" :size="44" />
            <div class="flex-grow-1">
                <label for="content" class="visually-hidden">Status</label>
                <textarea id="content" name="content" rows="3" data-char-limit="500"
                    class="form-control @error('content') is-invalid @enderror" placeholder="Apa yang ingin kamu bagikan?">{{ old('content') }}</textarea>
                @error('content')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                <div class="row g-2 mt-1">
                    <div class="col-sm-6">
                        <label for="image" class="meta">Unggah gambar</label>
                        <input type="file" id="image" name="image" accept="image/*"
                            class="form-control form-control-sm @error('image') is-invalid @enderror">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label for="image_url" class="meta">atau URL gambar</label>
                        <input type="url" id="image_url" name="image_url" value="{{ old('image_url') }}"
                            placeholder="https://..."
                            class="form-control form-control-sm @error('image_url') is-invalid @enderror">
                        @error('image_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mt-3">
                    <label for="post-location" class="form-label meta">Lokasi postingan (opsional)</label>
                    <div class="input-group input-group-sm">
                        <input type="text" id="post-location" name="location" maxlength="100" readonly
                            value="{{ old('location') }}" placeholder="Belum memilih kota"
                            class="form-control @error('location') is-invalid @enderror">
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse"
                            data-bs-target="#osm-location-search">Pilih kota</button>
                        <button type="button" id="clear-post-location" class="btn btn-outline-secondary"
                            @if (!old('location')) hidden @endif>Hapus</button>
                    </div>
                    @error('location')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="collapse mt-2" id="osm-location-search">
                        <div class="input-group input-group-sm">
                            <input type="search" id="location-search-query" maxlength="100"
                                class="form-control" placeholder="Cari nama kota">
                            <button type="button" id="search-post-location"
                                data-search-url="{{ route('locations.search') }}"
                                class="btn btn-outline-secondary">Cari di OpenStreetMap</button>
                        </div>
                        <div id="location-search-status" class="form-text" role="status" aria-live="polite"></div>
                        <div id="location-search-results" class="list-group mt-2" role="listbox"></div>
                        <div class="form-text">
                            Nama kota dikirim ke OpenStreetMap hanya saat tombol ditekan. ©
                            <a href="https://www.openstreetmap.org/copyright" target="_blank"
                                rel="noopener noreferrer">OpenStreetMap contributors</a>.
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="meta" data-char-counter aria-live="polite" hidden></span>
                    <button type="submit" data-char-submit class="btn btn-brand btn-sm px-4">Kirim status</button>
                </div>
            </div>
        </div>
    </form>

    {{-- Daftar status --}}
    @forelse ($posts as $post)
        <article id="post-{{ $post->id }}" class="cs-card p-3 mb-3">
            <div class="d-flex align-items-center gap-3 mb-2">
                <a href="{{ route('users.show', $post->user) }}" class="avatar-link" aria-label="Lihat profil {{ $post->user->name }}">
                    <x-avatar :user="$post->user" :size="44" />
                </a>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-baseline flex-wrap gap-2">
                        <a href="{{ route('users.show', $post->user) }}" class="name-link">{{ $post->user->name }}</a>
                        <x-user-meta :user="$post->user" :time="$post->created_at" :location="$post->location"
                            :show-profile-location="false" />
                    </div>
                </div>
                @if ($post->user_id === auth()->id())
                    <form method="POST" action="{{ route('posts.destroy', $post) }}"
                        onsubmit="konfirmasiHapus(event, 'Hapus status ini beserta komentarnya?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                    </form>
                @endif
            </div>

            @if ($post->content)
                <p class="post-text mb-2">{{ $post->content }}</p>
            @endif
            @if ($post->image)
                <img src="{{ $post->image_src }}" alt="Gambar status dari {{ $post->user->name }}" class="post-img mb-2"
                    loading="lazy">
            @endif

            <div class="d-flex align-items-center gap-2 mb-2">
                <form method="POST" action="{{ route('posts.like', $post) }}">
                    @csrf
                    <button class="act-btn {{ $post->liked_by_me ? 'liked' : '' }}"
                        aria-pressed="{{ $post->liked_by_me ? 'true' : 'false' }}">
                        {{ $post->liked_by_me ? '♥ Disukai' : '♡ Suka' }} · {{ $post->likes_count }}
                    </button>
                </form>
                <span class="meta">{{ $post->comments_count }} komentar</span>
            </div>

            {{-- Komentar utama + balasannya --}}
            @foreach ($post->rootComments as $c)
                @include('partials.comment', ['c' => $c, 'isReply' => false])
            @endforeach

            @php($bag = $errors->getBag('comment_' . $post->id))
            <form method="POST" action="{{ route('comments.store', $post) }}" enctype="multipart/form-data"
                class="comment">
                @csrf
                <x-avatar :user="auth()->user()" :size="34" />
                <div class="flex-grow-1">
                    <label for="comment-{{ $post->id }}" class="visually-hidden">Komentar</label>
                    <input type="text" id="comment-{{ $post->id }}" name="comment" data-char-limit="500"
                        value="{{ $bag->any() ? old('comment') : '' }}" placeholder="Tulis komentar..."
                        class="form-control form-control-sm @if ($bag->has('comment')) is-invalid @endif">
                    @if ($bag->has('comment'))
                        <div class="invalid-feedback">{{ $bag->first('comment') }}</div>
                    @endif
                    <div class="meta text-end" data-char-counter aria-live="polite" hidden></div>
                    <div class="row g-2 mt-0">
                        <div class="col-sm-6">
                            <input type="file" name="image" accept="image/*" aria-label="Gambar komentar"
                                class="form-control form-control-sm @if ($bag->has('image')) is-invalid @endif">
                            @if ($bag->has('image'))
                                <div class="invalid-feedback">{{ $bag->first('image') }}</div>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <input type="url" name="image_url" placeholder="atau URL gambar"
                                aria-label="URL gambar komentar" value="{{ $bag->any() ? old('image_url') : '' }}"
                                class="form-control form-control-sm @if ($bag->has('image_url')) is-invalid @endif">
                            @if ($bag->has('image_url'))
                                <div class="invalid-feedback">{{ $bag->first('image_url') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <button type="submit" data-char-submit class="btn btn-brand btn-sm align-self-start">Kirim</button>
            </form>
        </article>
    @empty
        <div class="cs-card p-4 text-center">
            <p class="mb-0">Belum ada status. Tulis yang pertama di kotak di atas.</p>
        </div>
    @endforelse

    <div class="mt-3">{{ $posts->links('pagination::bootstrap-5') }}</div>
@endsection
