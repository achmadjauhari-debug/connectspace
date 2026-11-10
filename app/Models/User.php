<?php

namespace App\Models;

use App\Support\ImageStorage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'avatar', 'bio'];

    protected $hidden = ['password', 'remember_token'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function getAvatarSrcAttribute(): ?string
    {
        return ImageStorage::url($this->avatar);
    }

    public function getInitialsAttribute(): string
    {
        return Str::of($this->name)->explode(' ')->filter()->take(2)
            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    }
}
