<?php

namespace App\Models;

use App\Support\ImageStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    const UPDATED_AT = null; // tabel comments hanya punya created_at

    protected $fillable = ['post_id', 'parent_id', 'user_id', 'comment', 'image'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /** Komentar induk (hanya terisi pada balasan). */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /** Balasan dari komentar ini, terlama di atas. */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at')->orderBy('id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }

    public function getImageSrcAttribute(): ?string
    {
        return ImageStorage::url($this->image);
    }
}
