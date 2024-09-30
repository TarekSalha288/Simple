<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;
    protected  $guarded=[];
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
    public function replays(): HasMany{
        return $this->hasMany(ReplayComment::class);
    }
    public function likes(): HasMany{
        return $this->hasMany(LikeComment::class);
    }

}
