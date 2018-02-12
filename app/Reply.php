<?php

namespace App;

use App\Events\ThreadHasNewReply;

class Reply extends Model
{
    use Favoritable, RecordsActivity, WithPolicy;

    protected $with = ['owner', 'favorites'];
    protected $appends = ['favoritesCount', 'isFavorited', 'can'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->thread->increment('replies_count');
        });

        static::deleted(function ($reply) {
            $reply->thread->decrement('replies_count');
        });
    }

    public function path($uri = ''): string
    {
        return $this->thread->path($uri) . '#reply-' . $this->id;
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }
}
