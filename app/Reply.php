<?php

namespace App;

class Reply extends Model
{
    use Favoritable, RecordsActivity, WithPolicy;

    protected $with = ['owner', 'favorites'];
    protected $appends = ['favoritesCount', 'isFavorited', 'can'];

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
