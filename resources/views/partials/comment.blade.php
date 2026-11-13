{{-- Variabel: $c (Comment), $isReply (bool) --}}
<div class="comment {{ $isReply ? 'comment-reply' : '' }}" id="comment-{{ $c->id }}">
    <a href="{{ route('users.show', $c->user) }}" class="avatar-link" aria-label="Lihat profil {{ $c->user->name }}">
        <x-avatar :user="$c->user" :size="$isReply ? 28 : 34" />
    </a>

    <div class="flex-grow-1">
        <div class="d-flex align-items-center flex-wrap column-gap-2">
            <a href="{{ route('users.show', $c->user) }}" class="name-link">{{ $c->user->name }}</a>
            <x-user-meta :user="$c->user" :time="$c->created_at" />
            @if ($c->user_id === auth()->id())
                <form method="POST" action="{{ route('comments.destroy', $c) }}"
                    onsubmit="konfirmasiHapus(event, 'Hapus komentar ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link btn-sm text-danger p-0 text-decoration-none">Hapus Komentar</button>
                </form>
            @endif
        </div>

        @if ($c->comment)
            <div class="post-text">{{ $c->comment }}</div>
        @endif
        @if ($c->image)
            <img src="{{ $c->image_src }}" alt="Lampiran komentar" class="comment-img" loading="lazy">
        @endif

        {{-- Aksi: like + balas --}}
        <div class="d-flex align-items-center gap-1 mt-1">
            <form method="POST" action="{{ route('comments.like', $c) }}">
                @csrf
                <button class="act-btn act-sm {{ $c->liked_by_me ? 'liked' : '' }}"
                    aria-pressed="{{ $c->liked_by_me ? 'true' : 'false' }}">
                    {{ $c->liked_by_me ? '♥ Disukai' : '♡ Suka' }} · {{ $c->likes_count }}
                </button>
            </form>
            <button type="button" class="act-btn act-sm" data-bs-toggle="collapse"
                data-bs-target="#reply-form-{{ $c->id }}" aria-controls="reply-form-{{ $c->id }}">
                Balas @if (!$isReply && $c->replies->isNotEmpty())· {{ $c->replies->count() }} @endif
            </button>
        </div>

        @unless ($isReply)
            {{-- Daftar balasan --}}
            @if ($c->replies->isNotEmpty())
                <div class="replies">
                    @foreach ($c->replies as $r)
                        @include('partials.comment', ['c' => $r, 'isReply' => true])
                    @endforeach
                </div>
            @endif

        @endunless

        {{-- Form balas (terbuka otomatis bila ada error validasi) --}}
        @php($rbag = $errors->getBag('reply_' . $c->id))
        <div class="collapse @if ($rbag->any()) show @endif" id="reply-form-{{ $c->id }}">
            <form method="POST" action="{{ route('comments.reply', $c) }}" enctype="multipart/form-data"
                class="comment comment-reply">
                @csrf
                <x-avatar :user="auth()->user()" :size="28" />
                <div class="flex-grow-1">
                    <label for="reply-{{ $c->id }}" class="visually-hidden">Balas komentar {{ $c->user->name }}</label>
                    <input type="text" id="reply-{{ $c->id }}" name="comment" data-char-limit="500"
                        value="{{ $rbag->any() ? old('comment') : '' }}"
                        placeholder="Balas {{ $c->user->name }}..."
                        class="form-control form-control-sm @if ($rbag->has('comment')) is-invalid @endif">
                    @if ($rbag->has('comment'))
                        <div class="invalid-feedback">{{ $rbag->first('comment') }}</div>
                    @endif
                    <div class="meta text-end" data-char-counter aria-live="polite" hidden></div>
                    <div class="row g-2 mt-0">
                        <div class="col-sm-6">
                            <input type="file" name="image" accept="image/*" aria-label="Gambar balasan"
                                class="form-control form-control-sm @if ($rbag->has('image')) is-invalid @endif">
                            @if ($rbag->has('image'))
                                <div class="invalid-feedback">{{ $rbag->first('image') }}</div>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <input type="url" name="image_url" placeholder="atau URL gambar balasan"
                                aria-label="URL gambar balasan" value="{{ $rbag->any() ? old('image_url') : '' }}"
                                class="form-control form-control-sm @if ($rbag->has('image_url')) is-invalid @endif">
                            @if ($rbag->has('image_url'))
                                <div class="invalid-feedback">{{ $rbag->first('image_url') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <button type="submit" data-char-submit class="btn btn-brand btn-sm align-self-start">Balas</button>
            </form>
        </div>
    </div>
</div>
