@extends('layouts.app')
@section('title', 'Edit Profil · ConnectSpace')

@section('content')
@php($pw = $errors->getBag('password'))

<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="cs-card p-4 mb-4">
    @csrf @method('PUT')
    <h1 class="h5 fw-bold mb-3">Profil</h1>

    <div class="d-flex align-items-center gap-3 mb-3">
        <x-avatar :user="$user" :size="72" />
        <div class="flex-grow-1">
            <label for="avatar" class="form-label fw-semibold mb-1">Foto profil</label>
            <input type="file" id="avatar" name="avatar" accept="image/*"
                   class="form-control form-control-sm @error('avatar') is-invalid @enderror">
            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <input type="url" name="avatar_url" value="{{ old('avatar_url') }}" placeholder="atau tempel URL gambar (https://...)"
                   aria-label="URL foto profil" class="form-control form-control-sm mt-2 @error('avatar_url') is-invalid @enderror">
            @error('avatar_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if ($user->avatar)
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="remove_avatar" value="1" id="remove_avatar">
                    <label class="form-check-label" for="remove_avatar">Hapus foto profil</label>
                </div>
            @endif
        </div>
    </div>

    <div class="mb-3">
        <label for="name" class="form-label fw-semibold">Nama pengguna</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
               class="form-control @error('name') is-invalid @enderror">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="bio" class="form-label fw-semibold">Bio</label>
        <textarea id="bio" name="bio" rows="3" maxlength="500"
                  class="form-control @error('bio') is-invalid @enderror">{{ old('bio', $user->bio) }}</textarea>
        @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <button class="btn btn-brand">Simpan profil</button>
</form>

<form method="POST" action="{{ route('profile.password') }}" class="cs-card p-4">
    @csrf @method('PUT')
    <h2 class="h5 fw-bold mb-3">Ubah password</h2>
    <div class="mb-3">
        <label for="current_password" class="form-label fw-semibold">Password lama</label>
        <input type="password" id="current_password" name="current_password"
               class="form-control @if($pw->has('current_password')) is-invalid @endif">
        @if ($pw->has('current_password'))<div class="invalid-feedback">{{ $pw->first('current_password') }}</div>@endif
    </div>
    <div class="mb-3">
        <label for="password" class="form-label fw-semibold">Password baru</label>
        <input type="password" id="password" name="password"
               class="form-control @if($pw->has('password')) is-invalid @endif">
        @if ($pw->has('password'))<div class="invalid-feedback">{{ $pw->first('password') }}</div>@endif
        <div class="form-text">Minimal 6 karakter.</div>
    </div>
    <div class="mb-3">
        <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi password baru</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
    </div>
    <button class="btn btn-brand">Ubah password</button>
</form>
@endsection
