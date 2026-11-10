<?php

namespace App\Models;

use App\Support\ImageStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    const UPDATED_AT = null; // tabel comments hanya punya created_at

    protected $fillable = ['post_id', 'user_id', 'comment', 'image'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function getImageSrcAttribute(): ?string
    {
        return ImageStorage::url($this->image);
    }
}
