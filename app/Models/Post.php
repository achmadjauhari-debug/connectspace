<?php

namespace App\Models;

use App\Support\ImageStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = ['user_id', 'content', 'image', 'location'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Semua komentar, termasuk balasan (dipakai untuk hitungan & penghapusan). */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at')->orderBy('id');
    }

    /** Hanya komentar utama (bukan balasan), untuk ditampilkan di feed. */
    public function rootComments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->orderBy('created_at')->orderBy('id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function getImageSrcAttribute(): ?string
    {
        return ImageStorage::url($this->image);
    }
}
