<?php

namespace App\Filters;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ThreadFilter extends Filter
{
    /**
     * @var string[]
     */
    protected $filters = ['by', 'popular', 'unanswered'];

    protected function by(string $username): Builder
    {
        $user = User::where('name', $username)->firstOrFail();

        return $this->builder->where('user_id', $user->id);
    }

    protected function popular()
    {
        return $this->builder
            ->orderBy('replies_count', 'desc')
            ->limit(10);
    }

    protected function unanswered()
    {
        return $this->builder->where('replies_count', 0);
    }
}