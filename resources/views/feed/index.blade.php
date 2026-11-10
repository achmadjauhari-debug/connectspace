@extends('layouts.app')
@section('title', 'Beranda · ConnectSpace')

@section('content')
{{-- Form posting --}}
<form id="form-posting" method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" class="cs-card p-3 mb-4">
    @csrf
    <div class="d-flex gap-3">
        <x-avatar :user="auth()->user()" :size="44" />
        <div class="flex-grow-1">
            <label for="content" class="visually-hidden">Status</label>
            <textarea id="content" name="content" rows="3" maxlength="2000"
                      class="form-control @error('content') is-invalid @enderror"
                      placeholder="Apa yang ingin kamu bagikan?">{{ old('content') }}</textarea>
            @error('content')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

            <div class="row g-2 mt-1">
                <div class="col-sm-6">
                    <label for="image" class="meta">Unggah gambar</label>
                    <input type="file" id="image" name="image" accept="image/*"
                           class="form-control form-control-sm @error('image') is-invalid @enderror">
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-6">
                    <label for="image_url" class="meta">atau URL gambar</label>
                    <input type="url" id="image_url" name="image_url" value="{{ old('image_url') }}" placeholder="https://..."
                           class="form-control form-control-sm @error('image_url') is-invalid @enderror">
                    @error('image_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="text-end mt-3"><button class="btn btn-brand btn-sm px-4">Kirim status</button></div>
        </div>
    </div>
</form>

{{-- Daftar status --}}
@forelse ($posts as $post)
    <article id="post-{{ $post->id }}" class="cs-card p-3 mb-3">
        <div class="d-flex align-items-center gap-3 mb-2">
            <x-avatar :user="$post->user" :size="44" />
            <div class="flex-grow-1">
                <div class="fw-bold">{{ $post->user->name }}</div>
                <div class="meta">{{ $post->created_at->format('d/m/Y H:i') }}</div>
            </div>
            @if ($post->user_id === auth()->id())
                <form method="POST" action="{{ route('posts.destroy', $post) }}"
                      onsubmit="return confirm('Hapus status ini beserta komentarnya?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
            @endif
        </div>

        @if ($post->content)<p class="post-text mb-2">{{ $post->content }}</p>@endif
        @if ($post->image)<img src="{{ $post->image_src }}" alt="Gambar status dari {{ $post->user->name }}" class="post-img mb-2" loading="lazy">@endif

        <div class="d-flex align-items-center gap-2 mb-2">
            <form method="POST" action="{{ route('posts.like', $post) }}">
                @csrf
                <button class="act-btn {{ $post->liked_by_me ? 'liked' : '' }}"
                        aria-pressed="{{ $post->liked_by_me ? 'true' : 'false' }}">
                    {{ $post->liked_by_me ? '♥ Disukai' : '♡ Suka' }} · {{ $post->likes_count }}
                </button>
            </form>
            <span class="meta">{{ $post->comments->count() }} komentar</span>
        </div>

        {{-- Komentar --}}
        @foreach ($post->comments as $c)
            <div class="comment">
                <x-avatar :user="$c->user" :size="34" />
                <div class="flex-grow-1">
                    <div><span class="fw-bold">{{ $c->user->name }}</span>
                        <span class="meta ms-1">{{ $c->created_at->format('d/m/Y H:i') }}</span></div>
                    @if ($c->comment)<div class="post-text">{{ $c->comment }}</div>@endif
                    @if ($c->image)<img src="{{ $c->image_src }}" alt="Lampiran komentar" class="comment-img" loading="lazy">@endif
                </div>
            </div>
        @endforeach

        @php($bag = $errors->getBag('comment_' . $post->id))
        <form method="POST" action="{{ route('comments.store', $post) }}" enctype="multipart/form-data" class="comment">
            @csrf
            <x-avatar :user="auth()->user()" :size="34" />
            <div class="flex-grow-1">
                <label for="comment-{{ $post->id }}" class="visually-hidden">Komentar</label>
                <input type="text" id="comment-{{ $post->id }}" name="comment" maxlength="1000"
                       value="{{ $bag->any() ? old('comment') : '' }}" placeholder="Tulis komentar..."
                       class="form-control form-control-sm @if($bag->has('comment')) is-invalid @endif">
                @if ($bag->has('comment'))<div class="invalid-feedback">{{ $bag->first('comment') }}</div>@endif
                <div class="row g-2 mt-0">
                    <div class="col-sm-6">
                        <input type="file" name="image" accept="image/*" aria-label="Gambar komentar"
                               class="form-control form-control-sm @if($bag->has('image')) is-invalid @endif">
                        @if ($bag->has('image'))<div class="invalid-feedback">{{ $bag->first('image') }}</div>@endif
                    </div>
                    <div class="col-sm-6">
                        <input type="url" name="image_url" placeholder="atau URL gambar" aria-label="URL gambar komentar"
                               value="{{ $bag->any() ? old('image_url') : '' }}"
                               class="form-control form-control-sm @if($bag->has('image_url')) is-invalid @endif">
                        @if ($bag->has('image_url'))<div class="invalid-feedback">{{ $bag->first('image_url') }}</div>@endif
                    </div>
                </div>
            </div>
            <button class="btn btn-brand btn-sm align-self-start">Kirim</button>
        </form>
    </article>
@empty
    <div class="cs-card p-4 text-center">
        <p class="mb-0">Belum ada status. Tulis yang pertama di kotak di atas.</p>
    </div>
@endforelse

<div class="mt-3">{{ $posts->links('pagination::bootstrap-5') }}</div>
@endsection
